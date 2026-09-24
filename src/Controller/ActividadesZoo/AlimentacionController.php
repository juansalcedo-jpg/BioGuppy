<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\AlimentacionModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class AlimentacionController{

    use ActividadZooHelpersTrait;
    use BitacoraTrait;

    public function Alimentacion(){
        $obj = new AlimentacionModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/Alimentacion.php';
    }

    public function postCreateAlimentacion(){

        $obj = new AlimentacionModel();

        $tanqueId       = $_POST['tanque_id'] ?? null;
        $fecha          = $_POST['fecha_actividad'] ?? null;
        $tipoPez        = $_POST['tipo_pez'] ?: null;
        $horario        = $_POST['horario'] ?: null;
        $tipoAlimento   = $_POST['tipo_alimentacion'] ?: null;

        if(empty($tanqueId) || empty($fecha) || empty($tipoPez) || empty($horario) || empty($tipoAlimento)){
            $_SESSION['error'] = "Ningun campo puede quedar vacío.";
            redirect(getUrl('ActividadesZoo','Alimentacion','Alimentacion'));
            exit();
        }

        $fechaMin = date('Y-m-d', strtotime('-2 days'));
        $fechaMax = date('Y-m-d');

        if ($fecha < $fechaMin || $fecha > $fechaMax) {
            $_SESSION['error'] = "La fecha debe estar entre $fechaMin y $fechaMax.";
            redirect(getUrl('ActividadesZoo', 'Alimentacion', 'Alimentacion'));
            exit();
        }


        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'ALIMENTACION');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, horadia, tipopez, tipoalimento, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :horadia, :tipopez, :tipoalimento, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':horadia'          => $horario,
            ':tipopez'          => $tipoPez,
            ':tipoalimento'     => $tipoAlimento,
        ]);

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadzoo")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesZoo', $nuevoId['id'] ?? null, null, 'ALIMENTACION');

        $_SESSION['exito'] = "La actividad de alimentación se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>