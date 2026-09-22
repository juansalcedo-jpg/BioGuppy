<?php

namespace BioGuppy\Controller\Inicio;

class InicioController{

    public function index(){

        // Inicio quedo como modulo publico en permisos.php (para no tener que
        // tocar la base de datos con tblrolaccion/tblaccion), asi que la sesion
        // se valida aca a mano para que nadie sin login pueda verlo.
        if(!isset($_SESSION['auth']) || $_SESSION['auth'] !== 'ok'){
            redirect("inicio/login.php");
            exit();
        }

        include_once __DIR__ . '/../../../view/Inicio/index.php';

    }

}

?>
