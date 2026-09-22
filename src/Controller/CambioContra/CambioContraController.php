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
                    Si el correo está registrado, recibirás un mensaje con instrucciones.
                    </div>
                </div>';
            return;
        }

        $codusuario = $usuario['codusuario'];

        //Generar token seguro
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash_hmac('sha256', $token, HASH_KEY_RECUPERACION);

        //Guardar en BD con expiración
        $sql = "INSERT INTO tblcodigorecuperacion (codusuario, codigo, fechaexpiracion)
        VALUES (:codusuario, :codigo, NOW() + INTERVAL '15 minutes')";

        $obj->insert($sql, [
            ':codusuario' => $codusuario,
            ':codigo'     => $tokenHash
        ]);

        //Crear link con token: apunta al login, que valida el token
        //automáticamente y abre el modal de nueva contraseña.
        $link = "http://localhost/BioGuppy/Web/inicio/login.php?token=" . urlencode($token);


        //Enviar correo
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            include_once __DIR__ . '/../../../lib/conf/email.php';

            $mail->setFrom('bioguppy@gmail.com');
            $mail->addAddress($correo);

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isHTML(true);

            $mail->Subject = "Recuperación de contraseña";
            $mail->Body    = "<h1>Recuperación de contraseña</h1>
                          <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
                          <p><a href='$link'>$link</a></p>";
            $mail->AltBody = "Copia y pega este enlace en tu navegador: $link";
            $mail->send();

            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                    <div>
                    Hemos enviado un enlace de recuperación a tu correo. Revisa tu bandeja de entrada y haz clic en él para continuar.
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

    public function validar_codigo()
    {
        $obj = new CambioContraModel();

        // Token recibido por GET
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                <div>Token inválido.</div>
              </div>';
            return;
        }

        // Hashear el token con tu clave secreta
        $tokenHash = hash_hmac('sha256', $token, HASH_KEY_RECUPERACION);

        // Buscar en BD
        $sql = "SELECT codusuario, codrecuperacion 
            FROM tblcodigorecuperacion
            WHERE codigo = :codigo
              AND estado = 'A'
              AND fechaexpiracion > CURRENT_TIMESTAMP";

        $result = $obj->select($sql, [':codigo' => $tokenHash]);
        $fila = $result->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            //Token válido
            $_SESSION['codusuarioReset'] = $fila['codusuario'];

            // Inhabilitar token para que no se reutilice
            $sql2 = "UPDATE tblcodigorecuperacion 
                 SET estado = 'I' 
                 WHERE codrecuperacion = :codrecuperacion";
            $obj->update($sql2, [':codrecuperacion' => $fila['codrecuperacion']]);

            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                <div>Token validado. Ahora puedes cambiar tu contraseña.</div>
              </div>';
        } else {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                <div>El enlace ha expirado o es inválido.</div>
              </div>';
        }
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
                // El usuario se identifica por el token ya validado en
                // validar_codigo(), no por un correo guardado en sesión.
                $codusuario = $_SESSION['codusuarioReset'] ?? null;

                if (empty($codusuario)) {
                    echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>
                            Tu sesión de recuperación expiró o no es válida. Solicita un nuevo enlace.
                            </div>
                        </div>';
                    return;
                }

                $sqlActual = "SELECT contrasena FROM tblusuario WHERE codusuario = :codusuario";
                $resultActual = $obj->select($sqlActual, [':codusuario' => $codusuario]);
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
                $sql = "UPDATE tblusuario SET contrasena = :contrasena WHERE codusuario = :codusuario";

                $execu = $obj->update($sql, [
                    ':contrasena' => $contraEncriptada,
                    ':codusuario' => $codusuario,
                ]);

                if($execu){
                    echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                            <div>
                            Contraseña cambiada exitosamente. Ya puedes iniciar sesión con tu nueva contraseña.
                            </div>
                        </div>';
                    unset($_SESSION['codusuarioReset']);
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
