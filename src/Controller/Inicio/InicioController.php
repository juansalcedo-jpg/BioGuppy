<?php

namespace BioGuppy\Controller\Inicio;

class InicioController{

    public function index(){

        // Inicio es un modulo comun (MODULOS_COMUNES en lib/permisos.php):
        // lo ve cualquier usuario con sesion, sin importar su rol.
        // Se deja esta validacion como respaldo.
        if(!isset($_SESSION['auth']) || $_SESSION['auth'] !== 'ok'){
            redirect("inicio/login.php");
            exit();
        }

        include_once __DIR__ . '/../../../view/Inicio/index.php';

    }

}

?>
