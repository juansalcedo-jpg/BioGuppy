<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\AlimentacionModel;
use PDO;

class AlimentacionController{

    use ActividadZooHelpersTrait;

    public function Alimentacion(){
        $obj = new AlimentacionModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/Alimentacion.php';
    }

    public function postCreateAlimentacion(){

        $obj = new AlimentacionModel();

        $tanqueId       = $_POST['tanque_id'] ?? null;
        $fecha          = $_POST['fecha_actividad'] ?? null;
        $horario        = $_POST['horario'] ?: null;
        $tipoAlimento   = $_POST['tipo_alimentacion'] ?: null;

        if(empty($tanqueId) || empty($fecha)){
            $_SESSION['error'] = "El tanque y la fecha son obligatorios.";
            redirect(getUrl('ActividadesZoo','Alimentacion','Alimentacion'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'ALIMENTACIÓN');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, horadia, tipoalimento, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :horadia, :tipoalimento, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':horadia'          => $horario,
            ':tipoalimento'     => $tipoAlimento,
        ]);

        $_SESSION['exito'] = "La actividad de alimentación se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>
