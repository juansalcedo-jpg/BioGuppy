<?php

    namespace BioGuppy\Controller\HistorialTer;

    use BioGuppy\Model\HistorialTer\HistorialTerModel;
    use BioGuppy\Controller\Traits\BitacoraTrait;
    use PDO;

    class HistorialTerController{

        use BitacoraTrait;

        private function consultarSeguro($obj, $sql, $params = []){
            try{
                return $obj->select($sql, $params);
            }catch(\Throwable $error){
                error_log("Consulta fallida en HistorialTerController: " . $error->getMessage());
                return false;
            }
        }

        public function listHistTer(){

            $obj = new HistorialTerModel();

            $fechaDesde       = $_GET['fechaDesde'] ?? '';
            $fechaHasta       = $_GET['fechaHasta'] ?? '';
            $codSitio         = $_GET['codsitio'] ?? '';
            $codTipoActividad = $_GET['codtipoactividad'] ?? '';

            $errorFechas = null;

            if(!empty($fechaDesde) && !empty($fechaHasta) && $fechaDesde > $fechaHasta){
                $errorFechas = "La fecha desde no puede ser mayor que la fecha hasta.";
            }

            $condiciones = [];
            $parametros  = [];

            if(!empty($fechaDesde) && !$errorFechas){
                $condiciones[] = "a.fecha >= :fechaDesde";
                $parametros[':fechaDesde'] = $fechaDesde;
            }
            if(!empty($fechaHasta) && !$errorFechas){
                $condiciones[] = "a.fecha <= :fechaHasta";
                $parametros[':fechaHasta'] = $fechaHasta;
            }
            if(!empty($codSitio)){
                $condiciones[] = "a.codsitio = :codsitio";
                $parametros[':codsitio'] = $codSitio;
            }
            if(!empty($codTipoActividad)){
                $condiciones[] = "a.codtipoactividad = :codtipoactividad";
                $parametros[':codtipoactividad'] = $codTipoActividad;
            }

            $where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

            $sql = "SELECT a.codactividad,
                           a.fecha,
                           a.ph,
                           a.temperatura,
                           a.larvasaedes,
                           a.pupas,
                           a.larvasculex,
                           a.larvas,
                           a.peces,
                           a.cantidadhembras,
                           a.cantidadmachos,
                           a.volumenagualitros,
                           a.observaciones,
                           a.estado,
                           t.nombreactividad,
                           s.nombresitio,
                           b.nombrebarrio,
                           u.nombreusuario,
                           u.apellidousuario
                    FROM tblactividadterreno a
                    JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                    JOIN tblsitio s ON s.codsitio = a.codsitio
                    JOIN tblbarrio b ON b.codbarrio = s.codbarrio
                    JOIN tblusuario u ON u.codusuario = a.codusuario
                    $where
                    ORDER BY a.fecha DESC, a.codactividad DESC";

            $actividades = $this->consultarSeguro($obj, $sql, $parametros);

            $sitios = $this->consultarSeguro($obj,
                "SELECT codsitio, nombresitio
                 FROM tblsitio
                 WHERE estado = 'A'
                 ORDER BY nombresitio ASC");

            $tiposActividad = $this->consultarSeguro($obj,
                "SELECT codtipoactividad, nombreactividad
                 FROM tbltipoactividadterreno
                 WHERE estado = 'A'
                 ORDER BY nombreactividad ASC");

            include_once __DIR__ . '/../../../view/HistorialTer/listHistTer.php';

        }

        public function delete(){

            $obj = new HistorialTerModel();

            $id = $_GET['id'] ?? null;

            if(empty($id)){
                $_SESSION['error'] = "Registro no válido.";
                redirect(getUrl('HistorialTer','HistorialTer','listHistTer'));
                exit();
            }

            $actual = $obj->select("SELECT estado FROM tblactividadterreno WHERE codactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

            if(!$actual){
                $_SESSION['error'] = "El registro no existe.";
                redirect(getUrl('HistorialTer','HistorialTer','listHistTer'));
                exit();
            }

            $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

            $obj->update("UPDATE tblactividadterreno SET estado = :estado WHERE codactividad = :id", [
                ':estado' => $nuevoEstado,
                ':id'     => $id,
            ]);

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'HistorialTer',
                $id,
                $actual['estado'] === 'A' ? 'Activo' : 'Inactivo',
                $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
            );

            $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
            redirect(getUrl('HistorialTer','HistorialTer','listHistTer'));
            exit();

        }

    }

?>
