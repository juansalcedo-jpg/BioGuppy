<?php

namespace BioGuppy\Controller\Acceso;

use BioGuppy\Model\Acceso\AccesoModel;
use PDO;

class AccesoController{

    private $obj;

    public function __construct($model = null){
        $this->obj = $model ?? new AccesoModel();
    }

    public function login(){

        $obj = $this->obj;

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
                        $_SESSION['modulo'] = 'Usuarios';
                        $_SESSION['controlador'] = 'Usuarios';
                        $_SESSION['funcion'] = 'listUsu';

                    } else if ($datosRol['nombrerol'] == 'Administrador') {

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAdmin.php";
                        $_SESSION['modulo'] = 'Dashboard';
                        $_SESSION['controlador'] = 'Dashboard';
                        $_SESSION['funcion'] = 'listDashboard';

                    }else if($datosRol['nombrerol'] == 'Coordinador Control Biologico'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncCoordinador.php";
                        $_SESSION['modulo'] = 'Zoocriadero';
                        $_SESSION['controlador'] = 'Zoocriadero';
                        $_SESSION['funcion'] = 'listZoo';

                    }else if($datosRol['nombrerol'] == 'Auxiliar Terreno'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxTerreno.php";
                        $_SESSION['modulo'] = 'ActividadesTer';
                        $_SESSION['controlador'] = 'ActividadesTer';
                        $_SESSION['funcion'] = 'listMisActividades';

                    }else if($datosRol['nombrerol'] == 'Auxiliar Zoocriadero'){

                        $_SESSION['menu_file'] = "../view/funcionesLateral/FuncAuxZoocriadero.php";
                        $_SESSION['modulo'] = 'ActividadesListZoo';
                        $_SESSION['controlador'] = 'ActividadesListZoo';
                        $_SESSION['funcion'] = 'ActividadesListZoo';
                        
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
