<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\AjusteNivelModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class AjusteNivelController{

    use ActividadZooHelpersTrait;
    use BitacoraTrait;

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

        // Rangos logicos: el pH se mide de 0 a 14, y la temperatura del agua
        // de un tanque de guppies no tiene sentido fuera de un rango razonable.
        if(!is_numeric($ph) || $ph < 0 || $ph > 14){
            $_SESSION['error'] = "El pH debe ser un número entre 0 y 14.";
            redirect(getUrl('ActividadesZoo','AjusteNivel','AjusteNivel'));
            exit();
        }

        if(!is_numeric($temperatura) || $temperatura < 0 || $temperatura > 40){
            $_SESSION['error'] = "La temperatura debe ser un número entre 0°C y 40°C.";
            redirect(getUrl('ActividadesZoo','AjusteNivel','AjusteNivel'));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
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

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadzoo")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesZoo', $nuevoId['id'] ?? null, null, 'AJUSTE DE NIVEL');

        $_SESSION['exito'] = "La actividad de ajuste de nivel se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>