<?php

namespace BioGuppy\Controller\Perfil;
use BioGuppy\Model\Perfil\PerfilModel;
use BioGuppy\Controller\Traits\BitacoraTrait;
use PDO;

class PerfilController{

    use BitacoraTrait;

    private const REGEX_CONTRASENA = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/';

    private const FOTO_MAX_BYTES = 2 * 1024 * 1024;
    private const FOTO_TIPOS = [
        'image/jpeg' => 'jpg',   // Fotos .jpg / .jpeg
        'image/png'  => 'png',   // Imágenes .png
        'image/webp' => 'webp',  // Imágenes .webp
    ];

    private function codUsuarioSesion(){
        $codusuario = $_SESSION['usu_id'] ?? null;

        if(empty($codusuario) || ($_SESSION['auth'] ?? '') !== 'ok'){
            redirect("inicio/login.php");
            exit();
        }
        return $codusuario;
    }

    private function volverAlPerfil($tipo, $mensaje){
        $_SESSION[$tipo] = $mensaje;
        redirect(getUrl('Perfil', 'Perfil', 'perfil'));
        exit();
    }

    public function perfil(){

        $codusuario = $this->codUsuarioSesion();
        $obj = new PerfilModel();

        $sql = "SELECT u.codusuario,
                       u.nombreusuario,
                       u.apellidousuario,
                       u.numerodocumento,
                       u.correo,
                       r.nombrerol,
                       d.nombredocumento
                FROM tblusuario u
                INNER JOIN tblrol r ON r.codrol = u.codrol
                INNER JOIN tbltipodocumento d ON d.codtipodocumento = u.codtipodocumento
                WHERE u.codusuario = :codusuario";

        $usuario = $obj->select($sql, [':codusuario' => $codusuario])->fetch(PDO::FETCH_ASSOC);

        if(!$usuario){
            redirect(getUrl('Acceso', 'Acceso', 'logout'));
            exit();
        }

        $fotoPerfil = fotoPerfilUrl($codusuario);

        if(empty($_SESSION['csrf_perfil'])){
            $_SESSION['csrf_perfil'] = bin2hex(random_bytes(32));
        }

        include_once __DIR__ . '/../../../view/Perfil/perfil.php';
    }

    public function postUpdatePerfil(){

        $codusuario = $this->codUsuarioSesion();
        $obj = new PerfilModel();
        if(!hash_equals($_SESSION['csrf_perfil'] ?? '', $_POST['csrf'] ?? '')){
            $this->volverAlPerfil('error', "La solicitud no es válida. Recarga la página e inténtalo de nuevo.");
        }

        $correo          = trim($_POST['correo'] ?? '');
        $contraActual    = $_POST['contrasenaActual'] ?? '';
        $contraNueva     = $_POST['nuevaContrasena'] ?? '';
        $contraConfirm   = $_POST['confirmarContrasena'] ?? '';
        $hayFoto         = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;

        $usuario = $obj->select(
            "SELECT correo, contrasena FROM tblusuario WHERE codusuario = :codusuario",
            [':codusuario' => $codusuario]
        )->fetch(PDO::FETCH_ASSOC);

        $cambiaCorreo = ($correo !== '' && $correo !== $usuario['correo']);
        $cambiaContra = ($contraNueva !== '' || $contraConfirm !== '');

        if(!$cambiaCorreo && !$cambiaContra && !$hayFoto){
            $this->volverAlPerfil('error', "No hay cambios para guardar.");
        }

        if($correo === ''){
            $this->volverAlPerfil('error', "El correo no puede estar vacío.");
        }

        if($cambiaCorreo || $cambiaContra){
            if($contraActual === '' || !password_verify($contraActual, $usuario['contrasena'])){
                $this->volverAlPerfil('error', "La contraseña actual es incorrecta.");
            }
        }

        if($cambiaCorreo){
            if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
                $this->volverAlPerfil('error', "El correo no es válido.");
            }

            $existe = $obj->select(
                "SELECT 1 FROM tblusuario WHERE correo = :correo AND codusuario <> :codusuario",
                [':correo' => $correo, ':codusuario' => $codusuario]
            )->fetchColumn();

            if($existe){
                $this->volverAlPerfil('error', "El correo ya está registrado por otro usuario.");
            }
        }

        if($cambiaContra){
            if(!preg_match(self::REGEX_CONTRASENA, $contraNueva)){
                $this->volverAlPerfil('error', "La nueva contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo (@#$%^&*!).");
            }
            if($contraNueva !== $contraConfirm){
                $this->volverAlPerfil('error', "Las contraseñas no coinciden.");
            }
            if(password_verify($contraNueva, $usuario['contrasena'])){
                $this->volverAlPerfil('error', "La nueva contraseña no puede ser igual a la actual.");
            }
        }

        $fotoTemp = null;  
        $fotoExt  = null; 
        if($hayFoto){
            $foto = $_FILES['foto'];

            if($foto['error'] !== UPLOAD_ERR_OK){
                $this->volverAlPerfil('error', "No se pudo subir la foto. Inténtalo de nuevo.");
            }
            if($foto['size'] > self::FOTO_MAX_BYTES){
                $this->volverAlPerfil('error', "La foto no puede pesar más de 2 MB.");
            }

            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($foto['tmp_name']);
            if(!isset(self::FOTO_TIPOS[$mime]) || @getimagesize($foto['tmp_name']) === false){
                $this->volverAlPerfil('error', "La foto debe ser una imagen JPG, PNG o WEBP.");
            }

            $fotoTemp = $foto['tmp_name'];
            $fotoExt  = self::FOTO_TIPOS[$mime];
        }


        if($cambiaCorreo){
            $obj->update(
                "UPDATE tblusuario SET correo = :correo WHERE codusuario = :codusuario",
                [':correo' => $correo, ':codusuario' => $codusuario]
            );
            $_SESSION['usu_correo'] = $correo;
+
            $this->registrarBitacora($obj, 'UPDATE', 'Perfil', $codusuario,
                "Correo: {$usuario['correo']}", "Correo: $correo");
        }

        if($cambiaContra){
            $obj->update(
                "UPDATE tblusuario SET contrasena = :contrasena WHERE codusuario = :codusuario",
                [':contrasena' => password_hash($contraNueva, PASSWORD_DEFAULT), ':codusuario' => $codusuario]
            );

            $this->registrarBitacora($obj, 'UPDATE', 'Perfil', $codusuario, null, "Cambio de contraseña");
        }

        if($fotoTemp){
            $carpeta = fotoPerfilCarpeta();
            if(!is_dir($carpeta)){
                mkdir($carpeta, 0755, true);
            }

            foreach(glob($carpeta . "usuario_{$codusuario}.*") as $anterior){
                unlink($anterior);
            }

            if(!move_uploaded_file($fotoTemp, $carpeta . "usuario_{$codusuario}.{$fotoExt}")){
                $this->volverAlPerfil('error', "No se pudo guardar la foto de perfil.");
            }
        }

        $this->volverAlPerfil('exito', "Los cambios se guardaron correctamente.");
    }
}
