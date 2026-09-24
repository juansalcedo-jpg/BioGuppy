<?php

namespace BioGuppy\Controller\ActividadesTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use BioGuppy\Controller\Traits\BitacoraTrait;
use PDO;

/*
 * Módulo "Registrar actividad de terreno".
 * "Mis actividades" (listar, editar, inhabilitar) quedó en ActividadesListTer.
 */
class ActividadesTerController
{
    use BitacoraTrait;
    use ActividadTerHelpersTrait;

    const RUTA_MODULO      = 'ActividadesTer';
    const RUTA_CONTROLADOR = 'ActividadesTer';

    private function mostrarActividad($tipo)
    {
        $obj = new ActividadesTerModel();
        $depositos = $this->obtenerDepositosActivos($obj);
        $modo = 'crear';
        $tipoActividad = $tipo;
        include_once __DIR__ . '/../../../view/ActividadesTer/getUpdateAct.php';
    }

    public function Inspeccion()
    {
        $this->mostrarActividad('Inspeccion');
    }
    public function Siembra()
    {
        $this->mostrarActividad('Siembra');
    }
    public function Seguimiento()
    {
        $this->mostrarActividad('Seguimiento');
    }
    public function Resiembra()
    {
        $this->mostrarActividad('Resiembra');
    }

    public function postCreate()
    {
        $obj = new ActividadesTerModel();
        $tipo = $_POST['tipo_actividad'] ?? null;

        if (!in_array($tipo, ['Inspeccion', 'Siembra', 'Seguimiento', 'Resiembra'], true)) {
            $this->error("Tipo de actividad no válido.", 'Inspeccion');
        }

        $deposito = $_POST['deposito_id'] ?? null;
        $fecha = $_POST['fecha_actividad'] ?? null;
        $hora = $_POST['hora_actividad'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        if (empty($deposito))
            $this->error("Debe seleccionar un depósito.", $tipo);
        $this->validarFechaHora($fecha, $hora, $tipo);

        $codTipo = $this->obtenerCodTipoActividad($obj, $tipo);
        if (!$codTipo)
            $this->error("No se encontró el tipo de actividad seleccionado.", $tipo);

        $datos = $this->datosActividad($tipo, $_POST, $tipo);

        $sql = "INSERT INTO tblactividadterreno
        (codtipoactividad,codsitio,codusuario,fecha,hora,ph,temperatura,larvasaedes,pupas,larvasculex,peces,larvas,
        cantidadhembras,cantidadmachos,tiempoaclimatacionmin,volumenagualitros,recolectarempacar,observaciones,estado)
        VALUES
        (:codtipoactividad,:codsitio,:codusuario,:fecha,:hora,:ph,:temperatura,:larvasaedes,:pupas,:larvasculex,:peces,:larvas,
        :cantidadhembras,:cantidadmachos,:tiempoaclimatacionmin,:volumenagualitros,:recolectarempacar,:observaciones,'A')";

        try {
            $obj->insert($sql, [
                ':codtipoactividad' => $codTipo,
                ':codsitio' => $deposito,
                ':codusuario' => $_SESSION['usu_id'],
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
                ':observaciones' => $observaciones
            ]);
        } catch (\Throwable $error) {
            error_log("Error al registrar actividad de terreno: " . $error->getMessage());
            $this->error("No fue posible guardar la actividad. Verifique los datos e intente nuevamente.", $tipo);
        }

        $nuevo = $obj->select("SELECT codactividad FROM tblactividadterreno
        WHERE codusuario=:codusuario ORDER BY codactividad DESC LIMIT 1", [
            ':codusuario' => $_SESSION['usu_id']
        ])->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora($obj, 'INSERT', 'ActividadesTer', $nuevo['codactividad'] ?? null, null, $tipo . ' — sitio #' . $deposito);
        $_SESSION['exito'] = "La actividad se registró exitosamente.";

        // Si el rol también tiene "Mis actividades" lo llevamos allá;
        // si no, vuelve al formulario de registro.
        if (usuarioTienePermiso('ActividadesListTer')) {
            redirect(getUrl('ActividadesListTer', 'ActividadesListTer', 'listMisActividades'));
        } else {
            redirect(getUrl('ActividadesTer', 'ActividadesTer', $tipo));
        }
        exit();
    }

    public function listActTer()
    {
        $obj = new ActividadesTerModel();
        $desde = $_GET['fechaDesde'] ?? '';
        $hasta = $_GET['fechaHasta'] ?? '';
        $sitio = $_GET['codsitio'] ?? '';
        $tipo = $_GET['codtipoactividad'] ?? '';
        $errorFechas = null;

        if ($desde && $hasta && $desde > $hasta)
            $errorFechas = "La fecha desde no puede ser mayor que la fecha hasta.";

        $cond = [];
        $params = [];
        if ($desde && !$errorFechas) {
            $cond[] = "a.fecha>=:desde";
            $params[':desde'] = $desde;
        }
        if ($hasta && !$errorFechas) {
            $cond[] = "a.fecha<=:hasta";
            $params[':hasta'] = $hasta;
        }
        if ($sitio) {
            $cond[] = "a.codsitio=:sitio";
            $params[':sitio'] = $sitio;
        }
        if ($tipo) {
            $cond[] = "a.codtipoactividad=:tipo";
            $params[':tipo'] = $tipo;
        }

        $where = $cond ? "WHERE " . implode(" AND ", $cond) : "";

        $actividades = $this->consultarSeguro($obj, "SELECT a.codactividad,a.fecha,t.nombreactividad AS tipo_actividad,
        s.nombresitio AS sitio,(u.nombreusuario || ' ' || u.apellidousuario) AS responsable,a.observaciones,a.estado
        FROM tblactividadterreno a
        INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad=a.codtipoactividad
        INNER JOIN tblsitio s ON s.codsitio=a.codsitio
        INNER JOIN tblusuario u ON u.codusuario=a.codusuario
        $where ORDER BY a.fecha DESC,a.codactividad DESC", $params);

        $sitios = $this->consultarSeguro($obj, "SELECT codsitio,nombresitio FROM tblsitio WHERE estado='A' ORDER BY nombresitio ASC");
        $tiposActividad = $this->consultarSeguro($obj, "SELECT codtipoactividad,nombreactividad FROM tbltipoactividadterreno WHERE estado='A' ORDER BY nombreactividad ASC");

        include_once __DIR__ . '/../../../view/ActividadesTer/listActTer.php';
    }
}
