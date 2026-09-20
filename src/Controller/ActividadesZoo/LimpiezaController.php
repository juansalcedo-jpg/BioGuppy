<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\LimpiezaModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class LimpiezaController{

    use ActividadZooHelpersTrait;
    use BitacoraTrait;

    public function Limpieza(){
        $obj = new LimpiezaModel();
        $tanques = $this->obtenerTanquesActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesZoo/Limpieza.php';
    }

    public function postCreateLimpieza(){

        $obj = new LimpiezaModel();

        $tanqueId          = $_POST['tanque_id'] ?? null;
        $fecha             = $_POST['fecha_actividad'] ?? null;
        $restregadoEsponja = isset($_POST['restregado_esponja']);
        $aspiradoManguera  = isset($_POST['aspirado_manguera']);

        if(empty($tanqueId) || empty($fecha)){
            $_SESSION['error'] = "El tanque y la fecha son obligatorios.";
            redirect(getUrl('ActividadesZoo','Limpieza','Limpieza'));
            exit();
        }

        if(!$restregadoEsponja && !$aspiradoManguera){
            $_SESSION['error'] = "Selecciona al menos un método de limpieza.";
            redirect(getUrl('ActividadesZoo','Limpieza','Limpieza'));
            exit();
        }

        if($restregadoEsponja && $aspiradoManguera){
            $metodo = 'AMBOS';
        }elseif($restregadoEsponja){
            $metodo = 'ESPONJA';
        }else{
            $metodo = 'SUCCIONADOR';
        }

        $codTipo = $this->obtenerCodTipoActividadZoo($obj, 'LIMPIEZA');

        $sql = "INSERT INTO public.tblactividadzoo
                    (codactividad, codtipoactividad, codtanque, codusuario, fecha, metodolimpieza, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codtanque, :codusuario, :fecha, :metodolimpieza, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codtanque'        => $tanqueId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':metodolimpieza'   => $metodo,
        ]);

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadzoo")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesZoo', $nuevoId['id'] ?? null, null, 'LIMPIEZA');

        $_SESSION['exito'] = "La actividad de limpieza se registró exitosamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }
}

?>