<?php

namespace BioGuppy\Controller\ActividadesListZoo;

use BioGuppy\Model\ActividadesListZoo\ActividadesListZooModel;
use PDO;

class ActividadesListZooController{

    private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en ActividadesListZooController: " . $error->getMessage());
            return false;
        }
    }

    private function obtenerZoocriaderosActivos($obj){
        $sql = "SELECT codzoocriadero AS id, nombrezoocriadero
                FROM tblzoocriadero
                WHERE estado = 'A'
                ORDER BY nombrezoocriadero ASC";
        return $this->consultarSeguro($obj, $sql);
    }

    private function sqlMisActividades($condicionExtra = ''){
        return "SELECT
                    a.codactividad AS id,
                    a.fecha,
                    t.nombreactividad AS tipo_actividad,
                    z.nombrezoocriadero AS zoocriadero,
                    tk.numerotanque AS tanque,
                    a.estado
                FROM tblactividadzoo a
                INNER JOIN tbltipoactividadzoo t ON t.codtipoactividad = a.codtipoactividad
                INNER JOIN tblzootanque tk ON tk.codtanque = a.codtanque
                INNER JOIN tblzoocriadero z ON z.codzoocriadero = tk.codzoocriadero
                WHERE a.codusuario = :codusuario
                $condicionExtra
                ORDER BY a.fecha DESC, a.codactividad DESC";
    }

    public function ActividadesListZoo(){

        $obj = new ActividadesListZooModel();

        $actividades = $this->consultarSeguro($obj, $this->sqlMisActividades(), [
            ':codusuario' => $_SESSION['usu_id'],
        ]);

        $zoocriaderos = $this->obtenerZoocriaderosActivos($obj);

        include_once __DIR__ . '/../../../view/ActividadesListZoo/ActividadesListZoo.php';

    }

    public function filtro(){

        $obj = new ActividadesListZooModel();

        $mes = $_POST['mes'] ?: null;
        $mesInicio = null;
        $mesFin = null;
        if ($mes) {
            $mesInicio = $mes . '-01';
            $mesFin = date('Y-m-d', strtotime($mesInicio . ' +1 month'));
        }

        $codzoocriadero = $_POST['codzoocriadero'] ?: null;
        $tipoActividad  = $_POST['tipoactividad'] ?: null;

        $condicion = "AND (:mesInicio::date IS NULL OR (a.fecha >= :mesInicio::date AND a.fecha < :mesFin::date))
                      AND (:codzoocriadero::integer IS NULL OR z.codzoocriadero = :codzoocriadero::integer)
                      AND (:tipoactividad::text IS NULL OR t.nombreactividad ILIKE :tipoactividad::text)";

        $actividades = $this->consultarSeguro($obj, $this->sqlMisActividades($condicion), [
            ':codusuario'     => $_SESSION['usu_id'],
            ':mesInicio'      => $mesInicio,
            ':mesFin'         => $mesFin,
            ':codzoocriadero' => $codzoocriadero,
            ':tipoactividad'  => $tipoActividad,
        ]);

        include_once __DIR__ . '/../../../view/ActividadesListZoo/filtroMisActividadesZoo.php';

    }

    public function getUpdate(){

        $obj = new ActividadesListZooModel();

        $id = $_GET['id'];

        $sql = "SELECT a.*, t.nombreactividad
                FROM tblactividadzoo a
                INNER JOIN tbltipoactividadzoo t ON t.codtipoactividad = a.codtipoactividad
                WHERE a.codactividad = :id";

        $actividad = $obj->select($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$actividad || $actividad['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
            exit();
        }

        include_once __DIR__ . '/../../../view/ActividadesListZoo/getUpdateActZoo.php';

    }

    public function postUpdate(){

        $obj = new ActividadesListZooModel();

        $codactividad = $_POST['codactividad'] ?? null;

        $sqlDueno = "SELECT codusuario FROM tblactividadzoo WHERE codactividad = :id";
        $dueno = $obj->select($sqlDueno, [':id' => $codactividad])->fetch(PDO::FETCH_ASSOC);

        if(!$dueno || $dueno['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
            exit();
        }

        $fecha = $_POST['fecha_actividad'] ?? null;

        if(empty($fecha)){
            $_SESSION['error'] = "La fecha es obligatoria.";
            redirect(getUrl('ActividadesListZoo','ActividadesListZoo','getUpdate',['id'=>$codactividad]));
            exit();
        }

        $sql = "UPDATE tblactividadzoo SET
                    fecha = :fecha,
                    horadia = :horadia,
                    tipopez = :tipopez,
                    tipoalimento = :tipoalimento,
                    pecesnacidos = :pecesnacidos,
                    pecesmuertos = :pecesmuertos,
                    metodolimpieza = :metodolimpieza,
                    ph = :ph,
                    temperatura = :temperatura,
                    porcentajeaguacambiada = :porcentajeaguacambiada,
                    observaciones = :observaciones
                WHERE codactividad = :codactividad";

        $restregadoEsponja = isset($_POST['restregado_esponja']);
        $aspiradoManguera  = isset($_POST['aspirado_manguera']);
        $metodoLimpieza = null;
        if($restregadoEsponja && $aspiradoManguera){
            $metodoLimpieza = 'AMBOS';
        }elseif($restregadoEsponja){
            $metodoLimpieza = 'ESPONJA';
        }elseif($aspiradoManguera){
            $metodoLimpieza = 'SUCCIONADOR';
        }

        $obj->update($sql, [
            ':fecha'                   => $fecha,
            ':horadia'                 => $_POST['horario'] ?: null,
            ':tipopez'                 => $_POST['tipo_pez'] ?: null,
            ':tipoalimento'            => $_POST['tipo_alimentacion'] ?: null,
            ':pecesnacidos'            => isset($_POST['peces_nacidos']) && $_POST['peces_nacidos'] !== '' ? $_POST['peces_nacidos'] : null,
            ':pecesmuertos'            => isset($_POST['peces_muertos']) && $_POST['peces_muertos'] !== '' ? $_POST['peces_muertos'] : null,
            ':metodolimpieza'          => $metodoLimpieza,
            ':ph'                      => isset($_POST['ph']) && $_POST['ph'] !== '' ? $_POST['ph'] : null,
            ':temperatura'             => isset($_POST['temperatura']) && $_POST['temperatura'] !== '' ? $_POST['temperatura'] : null,
            ':porcentajeaguacambiada'  => isset($_POST['porcentaje_agua']) && $_POST['porcentaje_agua'] !== '' ? $_POST['porcentaje_agua'] : null,
            ':observaciones'           => $_POST['observaciones'] ?? '',
            ':codactividad'            => $codactividad,
        ]);

        $_SESSION['exito'] = "El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }

    public function delete(){

        $obj = new ActividadesListZooModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Registro no válido.";
            redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
            exit();
        }

        $actual = $obj->select("SELECT estado, codusuario FROM tblactividadzoo WHERE codactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$actual || $actual['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para modificar este registro.";
            redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
            exit();
        }

        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblactividadzoo SET estado = :estado WHERE codactividad = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
        redirect(getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo'));
        exit();

    }

}

?>
