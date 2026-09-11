<?php

namespace BioGuppy\Controller\Usuarios;

use BioGuppy\Model\Usuarios\UsuariosModel;
use PDO;

class UsuariosController{

    public function createUsu(){

        $obj = new UsuariosModel();

        $sqldocu = "SELECT * FROM tbltipodocumento ORDER BY codtipodocumento ASC";
        $resultdocu = $obj->select($sqldocu);

        $sqlrol = "SELECT * FROM tblrol ORDER BY codrol ASC";
        $resultrol = $obj->select($sqlrol);

        include_once __DIR__ . '/../../../view/Usuarios/createUsu.php';

    }

    public function postcreateUsu(){

        $obj = new UsuariosModel();

        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $tipoDocumento = $_POST['tipoDocumento'] ?? null;
        $numeroDocumento = $_POST['numeroDocumento'];
        $correo = $_POST['correo'];
        $celular = $_POST['celular'];
        $rol = $_POST['rol'] ?? null;
        $contraseñaTemp = $_POST['contraseñaTemp'];

        $regexNombre = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/";

        //Validando que los campos no esten vacios
        if(empty(trim($nombre)) ||
            empty(trim($apellido)) ||
            empty(trim($tipoDocumento)) ||
            empty(trim($numeroDocumento)) ||
            empty(trim($correo)) ||
            empty(trim($celular)) ||
            empty(trim($rol)) ||
            empty(trim($contraseñaTemp))){
            $_SESSION['error'] = "Todos los campos son obligatorios";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }else if(!preg_match($regexNombre, $nombre)){
            $_SESSION['error'] = "El nombre no es válido.";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }else if(!preg_match($regexNombre, $apellido)){
            $_SESSION['error'] = "El apellido no es válido.";
            redirect(getUrl('Usuarios','Usuarios','createUsu'));
            exit();
        }
        


        
        
        

        

        //redirect(getUrl('Usuarios','Usuarios','listUsu'));

    }

    public function listUsu(){

        include_once __DIR__ . '/../../../view/Usuarios/listUsu.php';

    }

}
