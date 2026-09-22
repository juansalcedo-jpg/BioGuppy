<?php
    session_start();

    // Autoload de Composer: hace que todas las clases bajo src/ (namespace
    // BioGuppy\...) se puedan usar sin includes manuales.
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/permisos.php';

    function redirect($url){
        echo "<script>";
        echo "window.location.href='$url'";
        echo "</script>";
    }
    function dd($data){
        echo "<pre>";
        die(print_r($data));
    }
    function getUrl($modulo, $controlador, $funcion, $parametros = false,$pagina = false){

        if($pagina == false){
            $pagina = "index";
        }

        $url = "$pagina.php?modulo=$modulo&controlador=$controlador&funcion=$funcion";

        if($parametros != false){
            foreach($parametros as $key => $valor){
                $url .= "&$key=$valor";
            }
        }
        return $url;
    }

    function resolve(){
        $modulo = ucwords($_GET['modulo']); //Carpeta ej: Usuarios
        $controlador = ucwords($_GET['controlador']); //Clase ej: UsuariosController
        $funcion = $_GET['funcion']; //Metodo en la clase: getUsers

        if (!usuarioTienePermiso($modulo, $controlador, $funcion)) {

            if (!isset($_SESSION['nombre_rol'])) {
                redirect("inicio/login.php");
            } else {
                $_SESSION['error'] = "No tienes permisos para acceder a esa función.";
                redirect(getUrl(
                    $_SESSION['modulo'] ?? 'Usuarios',
                    $_SESSION['controlador'] ?? 'Usuarios',
                    $_SESSION['funcion'] ?? 'listUsu'
                ));
            }
            exit();
        }

        $nombreClase = "BioGuppy\\Controller\\$modulo\\{$controlador}Controller";

        if(class_exists($nombreClase)){

            $objeto = new $nombreClase(); //$objeto = new UsuariosController();

            if(method_exists($objeto,$funcion)){
                $objeto->$funcion();
            }else{
                echo "El metodo $funcion no existe en el controlador $controlador";
            }
        }else{
            echo "El controlador $controlador no existe en el modulo $modulo";
        }
    }

     //agregar foto de perfil en el modulo de perfil
    function fotoPerfilCarpeta(){
        return __DIR__ . '/../web/uploads/perfiles/';
    }

    // agrega la url de la imagen cargada, si no hay devuelve null
    function fotoPerfilUrl($codusuario){

        if(empty($codusuario)){
            return null;
        }
        // Busca jpg, .png, .webp).
        $archivos = glob(fotoPerfilCarpeta() . "usuario_" . (int)$codusuario . ".*");
        if(empty($archivos)){
            return null;
        }
        return "uploads/perfiles/" . basename($archivos[0]) . "?v=" . filemtime($archivos[0]);
    }
?>