<?php

namespace BioGuppy\Controller\Usuarios;

use BioGuppy\Model\Usuarios\UsuariosModel;
use PDO;

class UsuariosController{

    public function createUsu(){

        $obj = new UsuariosModel();

        $sqldocu = "SELECT * FROM tbltipodocumento ORDER BY codtipodocumento ASC";
        $resultdocu = $obj->select($sqldocu);

        $sqlrol = "SELECT * FROM tblrol ORDER BY codrol ASC";
        $resultrol = $obj->select($sqlrol);

        include_once __DIR__ . '/../../../view/Usuarios/createUsu.php';

    }

    public function postcreateUsu(){

        $obj = new UsuariosModel();

        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $tipoDocumento = $_POST['tipoDocumento'] ?? null;
        $numeroDocumento = $_POST['numeroDocumento'];
        $correo = $_POST['correo'];
        $celular = $_POST['celular'];
        $rol = $_POST['rol'] ?? null;
        $contraseñaTemp = $_POST['contraseñaTemp'];

        //Validando que los campos no esten vacios
        if(empty(trim($nombre)) ||
            empty(trim($apellido)) ||
            empty(trim($tipoDocumento)) ||
            empty(trim($numeroDocumento)) ||
            empty(trim($correo)) ||
            empty(trim($celular)) ||
            empty(trim($rol)) ||
            empty(trim($contraseñaTemp))){
            $_SESSION['error'] = "Todos los campos son obligatorios";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }


        //Validando nombre y apellido
        $regexNombre = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/";
        if(!preg_match($regexNombre, $nombre)){
            $_SESSION['error'] = "El nombre no es válido.";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }
        if(!preg_match($regexNombre, $apellido)){
            $_SESSION['error'] = "El apellido no es válido.";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }

        // Validación de número de documento según tipo
        switch ($tipoDocumento) {
            case 1:
                $regexdocu = "/^[0-9]{5,10}$/";
                if(!preg_match($regexdocu, $numeroDocumento)){
                    $_SESSION['error'] = "La cédula no es válida.";
                    redirect(getUrl('Usuarios','Usuarios','createUsu'));
                    exit();
                }
                break;
            case 2:
                $regexdocu = "/^[0-9]{6,11}$/";
                if(!preg_match($regexdocu, $numeroDocumento)){
                    $_SESSION['error'] = "La tarjeta de identidad no es válida.";
                    redirect(getUrl('Usuarios','Usuarios','createUsu'));
                    exit();
                }
                break;
            case 3:
                $regexdocu = "/^[0-9]{6,7}$/";
                if(!preg_match($regexdocu, $numeroDocumento)){
                    $_SESSION['error'] = "La cédula de extranjería no es válida.";
                    redirect(getUrl('Usuarios','Usuarios','createUsu'));
                    exit();
                }
                break;
            case 4:
                $regexdocu = "/^[A-Z0-9]{6,9}$/";
                if(!preg_match($regexdocu, $numeroDocumento)){
                    $_SESSION['error'] = "El pasaporte no es válido.";
                    redirect(getUrl('Usuarios','Usuarios','createUsu'));
                    exit();
                }
                break;
            default:
                $_SESSION['error'] = "Tipo de documento no reconocido.";
                redirect(getUrl('Usuarios','Usuarios','createUsu'));
                exit();
        }

        // Validación de correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            $_SESSION['error'] = "El correo no es válido.";
                redirect(getUrl('Usuarios','Usuarios','createUsu'));
                exit();
        }

        //Validación de celular
        $regexcelular = '/^(3\d{9})$/';
        if(!preg_match($regexcelular, $celular)){
            $_SESSION['error'] = "El número de celular no es válido.";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }

        $sql = "SELECT numerodocumento,correo,usutelefono FROM tblusuario
                WHERE numerodocumento = :numerodocumento
                    OR correo = :correo
                    OR usutelefono = :usutelefono";

        $Validacion_usuario = $obj->select($sql, [
            ':numerodocumento' => $numeroDocumento,
            ':correo'     => $correo,
            ':usutelefono'     => $celular,
        ]);
        $fila = $Validacion_usuario->fetch(PDO::FETCH_ASSOC);

        if($fila){
            if ($fila['numerodocumento'] === $numeroDocumento) {
                $_SESSION['error'] = "El número de documento ya está registrado.";
            } elseif ($fila['correo'] === $correo) {
                $_SESSION['error'] = "El correo ya está registrado.";
            } elseif ($fila['usutelefono'] === $celular) {
                $_SESSION['error'] = "El celular ya está registrado.";
            }

            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }

        $sql = "INSERT INTO public.tblusuario
            VALUES (
                DEFAULT,
                :codrol,
                :codtipodocumento,
                :numerodocumento,
                :nombreusuario,
                :apellidousuario,
                :usutelefono,
                :correo,
                :contrasena,
                DEFAULT,
                DEFAULT              
            )";

        $ingresar_usuario = $obj->select($sql, [
            ':codrol' => $rol,
            ':codtipodocumento'     => $tipoDocumento,
            ':numerodocumento'     => $numeroDocumento,
            ':nombreusuario'     => $nombre,
            ':apellidousuario'     => $apellido,
            ':usutelefono'     => $celular,
            ':correo'     => $correo,
            ':contrasena'     => $contraseñaTemp,
        ]);



        redirect(getUrl('Usuarios','Usuarios','listUsu'));

    }

    public function listUsu(){

        $obj = new UsuariosModel();

        $sql = "SELECT u.nombreusuario, u.apellidousuario, u.correo, r.nombrerol, nombredocumento, u.numerodocumento, u.estado FROM tblusuario u
                    INNER JOIN tblrol r ON r.codrol = u.codrol
                    INNER JOIN tbltipodocumento d ON d.codtipodocumento = u.codtipodocumento";

        $usuarios = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Usuarios/listUsu.php';

    }

}
