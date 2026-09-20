<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\NacidosMuertosModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class NacidosMuertosController{

    use ActividadZooHelpersTrait;
    use BitacoraTrait;

    public function NacidosMuertos(){
        $obj = new NacidosMuertosModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/NacidosMuertos.php';
    }

    public function postCreateNacidosMuertos(){

        $obj = new NacidosMuertosModel();

        $tanqueId     = $_POST['tanque_id'] ?? null;
        $fecha        = $_POST['fecha_actividad'] ?? null;
        $pecesNacidos = $_POST['peces_nacidos'] !== '' ? $_POST['peces_nacidos'] : 0;
        $pecesMuertos = $_POST['peces_muertos'] !== '' ? $_POST['peces_muertos'] : 0;

        if(empty($tanqueId) || empty($fecha)){
            $_SESSION['error'] = "El tanque y la fecha son obligatorios.";
            redirect(getUrl('ActividadesZoo','NacidosMuertos','NacidosMuertos'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'RECOLECCIÓN');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, pecesnacidos, pecesmuertos, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :pecesnacidos, :pecesmuertos, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':pecesnacidos'     => $pecesNacidos,
            ':pecesmuertos'     => $pecesMuertos,
        ]);

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadzoo")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesZoo', $nuevoId['id'] ?? null, null, 'RECOLECCIÓN');

        $_SESSION['exito'] = "El registro de nacidos/muertos se guardó exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>