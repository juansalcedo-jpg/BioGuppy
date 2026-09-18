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
        $ph              = $_POST['ph'] !== '' ? $_POST['ph'] : null;
        $temperatura     = $_POST['temperatura'] !== '' ? $_POST['temperatura'] : null;

        if(empty($tanqueId) || empty($fecha) || $ph === null || $temperatura === null){
            $_SESSION['error'] = "El tanque, la fecha, el pH y la temperatura son obligatorios.";
            redirect(getUrl('ActividadesZoo','AjusteNivel','AjusteNivel'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'AJUSTE DE NIVEL');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, ph, temperatura, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :ph, :temperatura, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':ph'               => $ph,
            ':temperatura'      => $temperatura,
        ]);

        $_SESSION['exito'] = "La actividad de ajuste de nivel se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>
