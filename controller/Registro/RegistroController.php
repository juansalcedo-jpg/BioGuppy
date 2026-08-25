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

                            if(preg_match('/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+\.(com|edu)(\.[a-z]{2})?$/', $usu_correo)){

                                if($usu_clave2 == $usu_clave1){

                                    if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/', $usu_clave2)){
                                        $usu_id = $obj->autoincrement("Usuarios","usu_id");

                                        $sql = "INSERT INTO usuarios (usu_id, usu_nombre, usu_apellido, usu_cedula, usu_correo, usu_clave)
                                            VALUES ($usu_id, '$usu_nombre', '$usu_apellido', '$usu_cedula', '$usu_correo', '$usu_clave2')";

                                        $obj->insert($sql);
                                
                                        $_SESSION['ConfirmarRegistro'] = "Te has registrado correctamente";
                                        $_SESSION['MostrarRegistro'] = true;
                                        redirect("inicio/login.php");
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