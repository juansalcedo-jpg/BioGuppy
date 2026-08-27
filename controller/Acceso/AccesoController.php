<?php

    include_once '../model/Acceso/AccesoModel.php';

    class AccesoController{
        public function login(){
            $obj = new AccesoModel();

            $usu_correo = $_POST['usu_correo'];
            $usu_clave = $_POST['usu_clave'];

            $sql = "SELECT * FROM usuarios WHERE usu_correo = '$usu_correo'";
            $usuario = $obj->select($sql);

            if(pg_num_rows($usuario) > 0){
                $usu = pg_fetch_assoc($usuario);
                $contraseñaBD = $usu['usu_clave'];

                if(password_verify($usu_clave, $contraseñaBD)){
                    $_SESSION['usu_nombre'] = $usu['usu_nombre'];
                    $_SESSION['usu_correo'] = $usu['usu_correo'];
                    $_SESSION['usu_id'] = $usu['usu_id'];
                    $_SESSION['auth'] = "ok";

                    $sqlrol = "SELECT rol, subrol FROM usuarios WHERE usu_correo = '$usu_correo'";
                    $rol = $obj->select($sqlrol);

                    if (pg_num_rows($rol) > 0) {
                        $datosRol = pg_fetch_assoc($rol);
                        echo $datosRol['rol'] . " - " . $datosRol['subrol'];
                        if($datosRol['rol'] == 4 && $datosRol['subrol'] == 2){
                            $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxTerreno.php";
                        }else if($datosRol['rol'] == 4 && $datosRol['subrol'] == 1){
                            $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxEco.php";
                        }
                    }
                    redirect("index.php");
                } else {
                    $_SESSION['ErrorLogin'] = "Correo o contraseña incorrectos";
                    redirect("inicio/login.php");
                }
            } else {
                $_SESSION['ErrorLogin'] = "Correo o contraseña incorrectos";
                redirect("inicio/login.php");
            }
        }
        public function logout(){

            session_destroy();
            redirect("inicio/login.php");

        }
    }

?>