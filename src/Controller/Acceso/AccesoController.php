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

            $sqlrol = "SELECT r.codrol, r.nombrerol, r.estado
                    FROM tblusuario u
                    INNER JOIN tblrol r
                    ON u.codrol = r.codrol
                    WHERE u.correo = :correo";

            $rol = $obj->select($sqlrol, [
                ':correo' => $usu_correo
            ]);

            if($rol->rowCount() > 0){

                $datosRol = $rol->fetch(PDO::FETCH_ASSOC);

                if(($usu['estado'] ?? '') !== 'A'){
                    $_SESSION['ErrorLogin'] = "La cuenta esta inactiva";
                    redirect("inicio/login.php");
                    return;
                }

                if(($datosRol['estado'] ?? 'A') !== 'A'){
                    $_SESSION['ErrorLogin'] = "El rol asignado a tu cuenta está inactivo. Comunícate con el administrador.";
                    redirect("inicio/login.php");
                    return;
                }

                $_SESSION['usu_nombre'] = $usu['nombreusuario'];
                $_SESSION['usu_correo'] = $usu['correo'];
                $_SESSION['usu_id']     = $usu['codusuario'];
                $_SESSION['auth']       = "ok";

                $_SESSION['nombre_rol'] = $datosRol['nombrerol'];
                $_SESSION['codrol']     = $datosRol['codrol'];

                // Ya no hay un menú fijo por nombre de rol: la barra lateral se
                // arma sola con los módulos que el rol tenga en tblrolmodulo.
                // Todos los roles llegan primero a Inicio.
                $_SESSION['modulo']      = 'Inicio';
                $_SESSION['controlador'] = 'Inicio';
                $_SESSION['funcion']     = 'index';

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