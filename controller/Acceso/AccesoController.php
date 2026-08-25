<?php

    include_once '../model/Acceso/AccesoModel.php';

    class AccesoController{
        public function login(){

            $obj = new AccesoModel();

            $usu_correo = $_POST['usu_correo'];
            $usu_clave = $_POST['usu_clave'];

            $sql = "SELECT * FROM usuarios WHERE usu_correo = '$usu_correo' AND usu_clave = '$usu_clave'";
            $usuario = $obj->select($sql);

            if(pg_num_rows($usuario)>0){
            
                while($usu=pg_fetch_assoc($usuario)){
                    $_SESSION['usu_nombre'] = $usu['usu_nombre'];
                    $_SESSION['usu_correo'] = $usu['usu_correo'];
                    $_SESSION['usu_id'] = $usu['usu_id'];
                    $_SESSION['auth'] = "ok";
                }
                redirect("index.php");
            }else{
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