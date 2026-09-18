<?php

    namespace BioGuppy\Controller\HistorialZoo;

    use BioGuppy\Model\HistorialZoo\HistorialZooModel;
    use PDO;

    class HistorialZooController{

        private function consultarSeguro($obj, $sql, $params = []){
            try{
                return $obj->select($sql, $params);
            }catch(\Throwable $error){
                error_log("Consulta fallida en HistorialZooController: " . $error->getMessage());
                return false;
            }
        }

        public function listHistZoo(){

            $obj = new HistorialZooModel();

            $fechaDesde       = $_GET['fechaDesde'] ?? '';
            $fechaHasta       = $_GET['fechaHasta'] ?? '';
            $codZoocriadero   = $_GET['codzoocriadero'] ?? '';
            $codTipoActividad = $_GET['codtipoactividad'] ?? '';

            $condiciones = [];
            $parametros  = [];

            if(!empty($fechaDesde)){
                $condiciones[] = "az.fecha >= :fechaDesde";
                $parametros[':fechaDesde'] = $fechaDesde;
            }
            if(!empty($fechaHasta)){
                $condiciones[] = "az.fecha <= :fechaHasta";
                $parametros[':fechaHasta'] = $fechaHasta;
            }
            if(!empty($codZoocriadero)){
                $condiciones[] = "z.codzoocriadero = :codzoocriadero";
                $parametros[':codzoocriadero'] = $codZoocriadero;
            }
            if(!empty($codTipoActividad)){
                $condiciones[] = "az.codtipoactividad = :codtipoactividad";
                $parametros[':codtipoactividad'] = $codTipoActividad;
            }

            $where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

            $sql = "SELECT az.codactividad,
                           az.fecha,
                           ta.nombreactividad AS tipo_actividad,
                           zt.numerotanque AS tanque,
                           z.nombrezoocriadero AS zoocriadero,
                           (u.nombreusuario || ' ' || u.apellidousuario) AS responsable,
                           az.observaciones,
                           az.estado
                    FROM tblactividadzoo az
                    JOIN tbltipoactividadzoo ta ON ta.codtipoactividad = az.codtipoactividad
                    JOIN tblzootanque zt ON zt.codtanque = az.codtanque
                    JOIN tblzoocriadero z ON z.codzoocriadero = zt.codzoocriadero
                    JOIN tblusuario u ON u.codusuario = az.codusuario
                    $where
                    ORDER BY az.fecha DESC, az.codactividad DESC";

            $actividades = $this->consultarSeguro($obj, $sql, $parametros);

            $zoocriaderos = $this->consultarSeguro($obj,
                "SELECT codzoocriadero, nombrezoocriadero
                 FROM tblzoocriadero
                 WHERE estado = 'A'
                 ORDER BY nombrezoocriadero ASC");

            $tiposActividad = $this->consultarSeguro($obj,
                "SELECT codtipoactividad, nombreactividad
                 FROM tbltipoactividadzoo
                 WHERE estado = 'A'
                 ORDER BY nombreactividad ASC");

            include_once __DIR__ . '/../../../view/HistorialZoo/listHistZoo.php';

        }

    }

?>