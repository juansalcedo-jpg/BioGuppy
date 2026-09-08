<?php

    include_once '../model/CambioContra/CambioContraModel.php';
    require __DIR__ . '/../../vendor/autoload.php';
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
            }else{
                $_SESSION['correoRecuperar'] = $correo;
                $codigo = rand(100000, 999999);
                $mail = new PHPMailer(true);

                $obj = new CambioContraModel();

                $sql = "INSERT INTO codigo_recu VALUES(DEFAULT, '$codigo')";

                $execute = $obj->insert($sql);

                try {

                    $mail->isSMTP();
                    include_once '../lib/conf/email.php';

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
        }

        public function validar_codigo(){

            $obj = new CambioContraModel();

            $codigo = (int) $_POST['codigo'];

            $sql = "SELECT * FROM codigo_recu WHERE numero_cod = $codigo AND estado=true";

            $codigo_validado = $obj->select($sql);

            if(pg_num_rows($codigo_validado) > 0){
                echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                        <div>
                        Código validado exitosamente.
                        </div>
                    </div>';
            }else{
                echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>
                        Código no valido.
                        </div>
                    </div>';
            }

            $sql2 = "UPDATE codigo_recu SET estado = false WHERE numero_cod = $codigo";

            $exe = $obj->update($sql2);

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
                    $contraEncriptada = password_hash($contra, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET usu_clave = '$contraEncriptada' WHERE usu_correo = '$correo'";

                    $execu = $obj->update($sql);

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


?>