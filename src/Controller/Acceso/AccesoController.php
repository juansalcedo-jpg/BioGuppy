<?php

namespace BioGuppy\Controller\Acceso;

use BioGuppy\Model\Acceso\AccesoModel;
use PDO;

class AccesoController{

    public function login(){

        $obj = new AccesoModel();

        $usu_correo = $_POST['usu_correo'];
        $usu_clave = $_POST['usu_clave'];

        $sql = "SELECT * 
                FROM tblusuario 
                WHERE correo = :correo";

        $usuario = $obj->select($sql, [
            ':correo' => $usu_correo
        ]);

        $usu = $usuario->fetch(PDO::FETCH_ASSOC);
        $contrasenaBD = $usu['contrasena'];

        if($usuario->rowCount() > 0 && password_verify($usu_clave, $contrasenaBD)){

            $sqlrol = "SELECT r.nombrerol
                    FROM tblusuario u
                    INNER JOIN tblrol r
                    ON u.codrol = r.codrol
                    WHERE u.correo = :correo";

            $rol = $obj->select($sqlrol, [
                ':correo' => $usu_correo
            ]);

            if($rol->rowCount() > 0){

                $sqlestado = "SELECT *
                        FROM tblusuario
                        WHERE correo = :correo
                        AND estado = 'A'";

                $estado = $obj->select($sqlestado, [
                    ':correo' => $usu_correo
                ]);

                if($estado->rowCount() > 0){
                    $_SESSION['usu_nombre'] = $usu['nombreusuario'];
                    $_SESSION['usu_correo'] = $usu['correo'];
                    $_SESSION['usu_id'] = $usu['codusuario'];
                    $_SESSION['auth'] = "ok";

                    $datosRol = $rol->fetch(PDO::FETCH_ASSOC);

                    $_SESSION['nombre_rol'] = $datosRol['nombrerol'];

                    if($datosRol['nombrerol'] == 'Super Admin'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncSuperAdmin.php";

                    }else if($datosRol['nombrerol'] == 'Admin'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAdmin.php";

                    }else if($datosRol['nombrerol'] == 'Coordinador Control Biologico'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncCoordinador.php";

                    }else if($datosRol['nombrerol'] == 'Auxiliar Terreno'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxTerreno.php";

                    }else if($datosRol['nombrerol'] == 'Auxiliar Zoocriadero'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxZoocriadero.php";
                    }
                }else{
                    $_SESSION['ErrorLogin'] = "La cuenta esta inactiva";
                    redirect("inicio/login.php");
                }

                redirect("index.php");
                }
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
