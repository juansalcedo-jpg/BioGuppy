<?php

include_once '../model/Registro/RegistroModel.php';

    class RegistroController{

        public function register(){
            $obj = new RegistroModel();

            $usu_nombre = $_POST['usu_nombre'];
            $usu_apellido = $_POST['usu_apellido'];
            $usu_cedula = $_POST['usu_cedula'];
            $usu_correo = $_POST['usu_correo'];
            $usu_clave1 = $_POST['usu_clave1'];
            $usu_clave2 = $_POST['usu_clave2'];

            if(!empty(trim($usu_nombre)) &&
                !empty(trim($usu_apellido)) &&
                !empty(trim($usu_cedula)) &&
                !empty(trim($usu_correo)) &&
                !empty(trim($usu_clave1)) &&
                !empty(trim($usu_clave2))) {


                if(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/', $usu_nombre)){

                    if(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/', $usu_apellido)){

                        if(preg_match('/^[0-9]{6,10}$/', $usu_cedula)){

                            if(filter_var($usu_correo, FILTER_VALIDATE_EMAIL)){

                                if($usu_clave2 == $usu_clave1){

                                    if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/', $usu_clave2)){

                                        $sqlVer = "SELECT * FROM usuarios WHERE usu_correo = '$usu_correo' OR usu_cedula = $usu_cedula";

                                        $verificar = $obj->select($sqlVer);

                                        if(pg_num_rows($verificar) == 0){
                                            $usu_id = $obj->autoincrement("Usuarios","usu_id");
                                        
                                            $conEncript = password_hash($usu_clave2, PASSWORD_DEFAULT);
                                            $sql = "INSERT INTO usuarios (usu_id, usu_nombre, usu_apellido, usu_cedula, usu_correo, usu_clave)
                                                VALUES ($usu_id, '$usu_nombre', '$usu_apellido', '$usu_cedula', '$usu_correo', '$conEncript')";

                                            $obj->insert($sql);
                                    
                                            $_SESSION['ConfirmarRegistro'] = "Te has registrado correctamente";
                                            $_SESSION['MostrarRegistro'] = true;
                                            redirect("inicio/login.php");
                                        }else{
                                            $_SESSION['ErrorValidacion'] = "El correo o la cedula ya estan registrados";
                                            $_SESSION['MostrarRegistro'] = true;
                                            redirect("inicio/login.php");
                                        }
                                    } else {
                                        $_SESSION['ErrorValidacion'] = "La contraseña debe tener al menos 8 caracteres, 
                                        incluir mayúscula, minúscula, número y símbolo especial.";
                                        $_SESSION['MostrarRegistro'] = true;
                                        redirect("inicio/login.php");
                                    }
                                } else {
                                    $_SESSION['ErrorValidacion'] = "Las contraseñas no coinciden";
                                    $_SESSION['MostrarRegistro'] = true;
                                    redirect("inicio/login.php");
                                }
                            } else {
                                $_SESSION['ErrorValidacion'] = "El correo no es valido. Ejemplo: juan.perez@ejemplo.com.co";
                                $_SESSION['MostrarRegistro'] = true;
                                redirect("inicio/login.php");
                            }
                        } else {
                            $_SESSION['ErrorValidacion'] = "Cédula inválida: solo números (6-10 dígitos).";
                            $_SESSION['MostrarRegistro'] = true;
                            redirect("inicio/login.php");
                        }
                    } else {
                        $_SESSION['ErrorValidacion'] = "El apellido no es valido. Solo se permiten letras y espacios.";
                        $_SESSION['MostrarRegistro'] = true;
                        redirect("inicio/login.php");
                    }
                }else{
                    $_SESSION['ErrorValidacion'] = "El nombre no es valido. Solo se permiten letras y espacios.";
                    $_SESSION['MostrarRegistro'] = true;
                    redirect("inicio/login.php");
                }

            } else {
                $_SESSION['ErrorDatos'] = "Faltan datos";
                $_SESSION['MostrarRegistro'] = true;
                redirect("inicio/login.php");
            }
        }

    }

?>