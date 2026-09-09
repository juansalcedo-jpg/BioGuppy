<?php

    include_once '../model/Usuarios/UsuariosModel.php';

    class UsuariosController{

        public function createUsu(){

            include_once "../view/Usuarios/createUsu.php";
        
        }

        public function listUsu(){

        include_once "../view/Usuarios/listUsu.php";
        
        }

    }

?>