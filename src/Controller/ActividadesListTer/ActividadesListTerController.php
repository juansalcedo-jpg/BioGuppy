<?php

namespace BioGuppy\Controller\ActividadesListTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use BioGuppy\Controller\Traits\BitacoraTrait;
use BioGuppy\Controller\ActividadesTer\ActividadTerHelpersTrait;
use PDO;

class ActividadesListTerController
{
    use BitacoraTrait;
    use ActividadTerHelpersTrait;

    const RUTA_MODULO      = 'ActividadesListTer';
    const RUTA_CONTROLADOR = 'ActividadesListTer';

    private function sqlMisActividades($extra = '')
    {
        return "SELECT a.codactividad AS id,a.fecha,t.nombreactividad AS tipo_actividad,
        td.nombretipodeposito AS deposito,s.nombresitio AS sitio,a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE a.codusuario=:codusuario
        $extra
        ORDER BY a.fecha DESC,a.codactividad DESC";
    }

    public function listMisActividades()
    {
        $obj = new ActividadesTerModel();

        $desde = $_GET['fechaDesde'] ?? '';
        $hasta = $_GET['fechaHasta'] ?? '';
        $sitio = $_GET['codsitio'] ?? '';
        $tipo  = $_GET['codtipoactividad'] ?? '';

        $errorFechas = null;
        if ($desde && $hasta && $desde > $hasta) {
            $errorFechas = "La fecha desde no puede ser mayor que la fecha hasta.";
        }

        $extra = '';
        $params = [':codusuario' => $_SESSION['usu_id']];

        if ($desde && !$errorFechas) {
            $extra .= " AND a.fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if ($hasta && !$errorFechas) {
            $extra .= " AND a.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if ($sitio) {
            $extra .= " AND a.codsitio = :sitio";
            $params[':sitio'] = $sitio;
        }
        if ($tipo) {
            $extra .= " AND a.codtipoactividad = :tipo";
            $params[':tipo'] = $tipo;
        }

        $actividades = $this->consultarSeguro($obj, $this->sqlMisActividades($extra), $params);
        $depositos = $this->obtenerDepositosActivos($obj);

        $sitios = $this->consultarSeguro($obj, "SELECT codsitio,nombresitio FROM tblsitio WHERE estado='A' ORDER BY nombresitio ASC");
        $tiposActividad = $this->consultarSeguro($obj, "SELECT codtipoactividad,nombreactividad FROM tbltipoactividadterreno WHERE estado='A' ORDER BY nombreactividad ASC");

        include_once __DIR__ . '/../../../view/ActividadesTer/listMisActividades.php';
    }

    public function filtro()
    {
        $obj = new ActividadesTerModel();
        $mes = !empty($_POST['mes']) ? $_POST['mes'] : null;

        if ($mes && substr($mes, 0, 4) !== date('Y')) {
            $mes = null;
        }
        $mesInicio = $mes ? $mes . '-01' : null;
        $mesFin = $mes ? date('Y-m-d', strtotime($mesInicio . ' +1 month')) : null;
        $coddeposito = !empty($_POST['coddeposito']) ? $_POST['coddeposito'] : null;
        $tipo = !empty($_POST['tipoactividad']) ? $_POST['tipoactividad'] : null;

        $extra = "AND (:mesInicio::date IS NULL OR (a.fecha>=:mesInicio::date AND a.fecha<:mesFin::date))
        AND (:coddeposito::integer IS NULL OR a.codsitio=:coddeposito::integer)
        AND (:tipoactividad::text IS NULL OR t.nombreactividad ILIKE :tipoactividad::text)";

        $actividades = $this->consultarSeguro($obj, $this->sqlMisActividades($extra), [
            ':codusuario' => $_SESSION['usu_id'],
            ':mesInicio' => $mesInicio,
            ':mesFin' => $mesFin,
            ':coddeposito' => $coddeposito,
            ':tipoactividad' => $tipo
        ]);

        include_once __DIR__ . '/../../../view/ActividadesTer/filtroMisActividades.php';
    }

    public function getUpdate()
    {
        $obj = new ActividadesTerModel();
        $id = $_GET['id'] ?? null;
        if (empty($id))
            $this->error("Registro no válido.", 'listMisActividades');

        $actividad = $obj->select("SELECT a.*,t.nombreactividad
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        WHERE a.codactividad=:id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if (!$actividad || $actividad['codusuario'] != $_SESSION['usu_id']) {
            $this->error("No tiene permisos para editar este registro.", 'listMisActividades');
        }

        $modo = 'editar';
        $tipoActividad = $this->detectarTipo($actividad['nombreactividad']);
        include_once __DIR__ . '/../../../view/ActividadesTer/getUpdateAct.php';
    }

    public function postUpdate()
    {
        $obj = new ActividadesTerModel();
        $id = $_POST['codactividad'] ?? null;
        if (empty($id))
            $this->error("Registro no válido.", 'listMisActividades');

        $actual = $obj->select("SELECT a.*,t.nombreactividad
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        WHERE a.codactividad=:id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if (!$actual || $actual['codusuario'] != $_SESSION['usu_id']) {
            $this->error("No tiene permisos para editar este registro.", 'listMisActividades');
        }

        $tipo = $this->detectarTipo($actual['nombreactividad']);
        if (!$tipo)
            $this->error("Tipo de actividad no válido.", 'listMisActividades');

        $retorno = ['id' => $id];
        $fecha = $_POST['fecha_actividad'] ?? null;
        $hora = $_POST['hora_actividad'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        $this->validarFechaHora($fecha, $hora, 'getUpdate', $retorno, false);
        $datos = $this->datosActividad($tipo, $_POST, 'getUpdate', $retorno, $actual);

        $sql = "UPDATE tblactividadterreno SET
        fecha=:fecha,hora=:hora,ph=:ph,temperatura=:temperatura,
        larvasaedes=:larvasaedes,pupas=:pupas,larvasculex=:larvasculex,
        peces=:peces,larvas=:larvas,cantidadhembras=:cantidadhembras,
        cantidadmachos=:cantidadmachos,tiempoaclimatacionmin=:tiempoaclimatacionmin,
        volumenagualitros=:volumenagualitros,recolectarempacar=:recolectarempacar,
        observaciones=:observaciones
        WHERE codactividad=:id";

        try {
            $obj->update($sql, [
                ':fecha' => $fecha,
                ':hora' => $hora,
                ':ph' => $datos['ph'],
                ':temperatura' => $datos['temperatura'],
                ':larvasaedes' => $datos['larvasaedes'],
                ':pupas' => $datos['pupas'],
                ':larvasculex' => $datos['larvasculex'],
                ':peces' => $datos['peces'],
                ':larvas' => $datos['larvas'],
                ':cantidadhembras' => $datos['cantidadhembras'],
                ':cantidadmachos' => $datos['cantidadmachos'],
                ':tiempoaclimatacionmin' => $datos['tiempoaclimatacionmin'],
                ':volumenagualitros' => $datos['volumenagualitros'],
                ':recolectarempacar' => $datos['recolectarempacar'],
                ':observaciones' => $observaciones,
                ':id' => $id
            ]);
        } catch (\Throwable $error) {
            error_log("Error al actualizar actividad de terreno: " . $error->getMessage());
            $this->error("No fue posible actualizar la actividad. Verifique los datos e intente nuevamente.", 'getUpdate', $retorno);
        }

        $this->registrarBitacora($obj, 'UPDATE', 'ActividadesTer', $id, null, 'Actividad #' . $id . ' editada');
        $_SESSION['exito'] = "El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesListTer', 'ActividadesListTer', 'listMisActividades'));
        exit();
    }

    public function delete()
    {
        $obj = new ActividadesTerModel();
        $id = $_GET['id'] ?? null;
        $rol = $_SESSION['nombre_rol'] ?? '';
        $coordinador = $rol === 'Coordinador Control Biologico';

        // El coordinador viene del listado general (ActividadesTer/listActTer);
        // el auxiliar viene de "Mis actividades".
        $urlRetorno = $coordinador
            ? getUrl('ActividadesTer', 'ActividadesTer', 'listActTer')
            : getUrl('ActividadesListTer', 'ActividadesListTer', 'listMisActividades');

        if (empty($id)) {
            $_SESSION['error'] = "Registro no válido.";
            redirect($urlRetorno);
            exit();
        }

        $actual = $obj->select("SELECT estado,codusuario FROM tblactividadterreno WHERE codactividad=:id", [
            ':id' => $id
        ])->fetch(PDO::FETCH_ASSOC);

        $propietario = $actual && $actual['codusuario'] == $_SESSION['usu_id'];

        if (!$actual || (!$propietario && !$coordinador)) {
            $_SESSION['error'] = "No tiene permisos para modificar este registro.";
            redirect($urlRetorno);
            exit();
        }

        $nuevoEstado = $actual['estado'] === 'A' ? 'I' : 'A';
        $obj->update("UPDATE tblactividadterreno SET estado=:estado WHERE codactividad=:id", [
            ':estado' => $nuevoEstado,
            ':id' => $id
        ]);

        $this->registrarBitacora($obj, 'UPDATE', 'ActividadesTer', $id, $actual['estado'], $nuevoEstado);
        $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
        redirect($urlRetorno);
        exit();
    }
}
