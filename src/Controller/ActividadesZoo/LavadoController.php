<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\LavadoModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class LavadoController{

    use ActividadZooHelpersTrait;
    use BitacoraTrait;

    public function Lavado(){
        $obj = new LavadoModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/Lavado.php';
    }

    public function postCreateLavado(){

        $obj = new LavadoModel();

        $tanqueId       = $_POST['tanque_id'] ?? null;
        $fecha          = $_POST['fecha_actividad'] ?? null;
        $porcentajeAgua = $_POST['porcentaje_agua'] !== '' ? $_POST['porcentaje_agua'] : null;

        if(empty($tanqueId) || empty($fecha) || $porcentajeAgua === null){
            $_SESSION['error'] = "El tanque, la fecha y el porcentaje de agua cambiada son obligatorios.";
            redirect(getUrl('ActividadesZoo','Lavado','Lavado'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'LAVADO');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, porcentajeaguacambiada, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :porcentajeagua, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':porcentajeagua'   => $porcentajeAgua,
        ]);

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadzoo")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesZoo', $nuevoId['id'] ?? null, null, 'LAVADO');

        $_SESSION['exito'] = "La actividad de lavado se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>