<?php

namespace BioGuppy\Controller\CambioContra;

use BioGuppy\Model\CambioContra\CambioContraModel;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CambioContraController{

    public function enviarCorreo(){
        $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';

        if (empty($correo)){
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    Debes ingresar un correo electrónico.
                    </div>
                </div>';
            return;
        }else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    El correo ingresado no es válido.
                    </div>
                </div>';
            return;
        }

        $obj =  new CambioContraModel();

        $sqlUsuario = "SELECT codusuario FROM tblusuario WHERE correo = :correo AND estado = 'A'";
        $resultUsuario = $obj->select($sqlUsuario, [':correo' => $correo]);
        $usuario = $resultUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$usuario){
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    No existe una cuenta asociada a ese correo.
                    </div>
                </div>';
            return;
        }

        $codusuario = $usuario['codusuario'];
        $_SESSION['correoRecuperar'] = $correo;


        do {
            $codigo = random_int(100000, 999999);
            $codigoHash = hash_hmac('sha256', (string) $codigo, HASH_KEY_RECUPERACION);

            $sqlCheck = "SELECT COUNT(*) as total FROM tblcodigorecuperacion WHERE codigo = :codigo";
            $resultCheck = $obj->select($sqlCheck, [':codigo' => $codigoHash]);
            $row = $resultCheck->fetch(PDO::FETCH_ASSOC);
        } while ($row['total'] > 0);

        $sql = "INSERT INTO tblcodigorecuperacion (codusuario, codigo, fechaexpiracion)
                        VALUES (:codusuario, :codigo, NOW() + INTERVAL '10 minutes')";
        $execute = $obj->insert($sql, [
            ':codusuario' => $codusuario,
            ':codigo'     => $codigoHash,
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            include_once __DIR__ . '/../../../lib/conf/email.php';

            $mail->setFrom('bioguppy@gmail.com');
            $mail->addAddress($correo);

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isHTML(true);

            $mail->Subject = "Código de recuperación de contraseña";
            $mail->Body    = "<h1>Recuperación de contraseña</h1>
                            <p>Tu código de verificación es: <b>$codigo</b></p>";
            $mail->AltBody = "Tu código de verificación es: $codigo";
            $mail->send();

            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                    <div>
                    Hemos enviado un código de recuperación a tu correo.
                    </div>
                </div>';
        }catch (Exception $e) {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    No se pudo enviar el correo: '.$mail->ErrorInfo.'
                    </div>
                </div>';
        }
    }

    public function validar_codigo(){

        $obj = new CambioContraModel();

        $codigoIngresado = trim($_POST['codigo'] ?? '');
        $correo = $_SESSION['correoRecuperar'] ?? '';

        $sqlUsuario = "SELECT codusuario FROM tblusuario WHERE correo = :correo AND estado = 'A'";
        $resultUsuario = $obj->select($sqlUsuario, [':correo' => $correo]);
        $usuario = $resultUsuario->fetch(PDO::FETCH_ASSOC);

        $codusuario = $usuario['codusuario'];
        $codigoHash = hash_hmac('sha256', $codigoIngresado, HASH_KEY_RECUPERACION);

        $sql = "SELECT codrecuperacion FROM tblcodigorecuperacion
                WHERE codusuario = :codusuario
                    AND codigo = :codigo
                    AND estado = 'A'
                    AND fechaexpiracion > CURRENT_TIMESTAMP";

        $codigo_validado = $obj->select($sql, [
            ':codusuario' => $codusuario,
            ':codigo'     => $codigoHash,
        ]);
        $fila = $codigo_validado->fetch(PDO::FETCH_ASSOC);

        if($fila){
            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                    <div>
                    Código validado exitosamente.
                    </div>
                </div>';
        }else{
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    Código no válido.
                    </div>
                </div>';
        }

        $sql2 = "UPDATE tblcodigorecuperacion SET estado = 'I' WHERE codusuario = :codusuario AND codigo = :codigo";

        $exe = $obj->update($sql2, [
            ':codusuario' => $codusuario,
            ':codigo'     => $codigoHash,
        ]);

    }

    public function cambiar_contraseña(){
        $obj = new CambioContraModel();

        $contra = $_POST['nuevaContrasena'];
        $contraConfirm = $_POST['confirmarContrasena'];
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/';

        if(!preg_match($regex, $contra)){
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    La contraseña es invalida.
                    </div>
                </div>';
        }else{
            if($contra != $contraConfirm){
                echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                    Las contraseñas no coinciden.
                    </div>
                </div>';
            }else{
                $correo = $_SESSION['correoRecuperar'];

                $sqlActual = "SELECT contrasena FROM tblusuario WHERE correo = :correo";
                $resultActual = $obj->select($sqlActual, [':correo' => $correo]);
                $usuarioActual = $resultActual->fetch(PDO::FETCH_ASSOC);

                if($usuarioActual && password_verify($contra, $usuarioActual['contrasena'])){
                    echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>
                            La nueva contraseña no puede ser igual a la actual.
                            </div>
                        </div>';
                    return;
                }

                $contraEncriptada = password_hash($contra, PASSWORD_DEFAULT);
                $sql = "UPDATE tblusuario SET contrasena = :contrasena WHERE correo = :correo";

                $execu = $obj->update($sql, [
                    ':contrasena' => $contraEncriptada,
                    ':correo'     => $correo,
                ]);

                if($execu){
                    echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                            <div>
                            Contraseña cambiada exitosamente.
                            </div>
                        </div>';
                    unset($_SESSION['correoRecuperar']);
                }else{
                    echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>
                            Ocurrio un error al cambiar la contraseña.
                            </div>
                        </div>';
                }
            }
        }
    }
}
