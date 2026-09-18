<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\AjusteNivelModel;
use PDO;

class AjusteNivelController{

    use ActividadZooHelpersTrait;

    public function AjusteNivel(){
        $obj = new AjusteNivelModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/AjusteNivel.php';
    }

    public function postCreateAjusteNivel(){

        $obj = new AjusteNivelModel();

        $tanqueId        = $_POST['tanque_id'] ?? null;
        $fecha           = $_POST['fecha_actividad'] ?? null;
        $cantidadAgua    = $_POST['cantidad_agua'] !== '' ? $_POST['cantidad_agua'] : null;

        if(empty($tanqueId) || empty($fecha) || $cantidadAgua === null){
            $_SESSION['error'] = "El tanque, la fecha y la cantidad de agua son obligatorios.";
            redirect(getUrl('ActividadesZoo','AjusteNivel','AjusteNivel'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'AJUSTE DE NIVEL');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, cantidadaguaadicionada, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :cantidadagua, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':cantidadagua'     => $cantidadAgua,
        ]);

        $_SESSION['exito'] = "La actividad de ajuste de nivel se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>
