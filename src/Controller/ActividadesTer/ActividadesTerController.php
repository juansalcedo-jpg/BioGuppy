<?php

namespace BioGuppy\Controller\ActividadesTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class ActividadesTerController{

    use BitacoraTrait;

    private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en ActividadesTerController: " . $error->getMessage());
            return false;
        }
    }

// busca el codigo de un tipo de actividad de terreno por su nombre en el catalogo tbltipoactividadterreno

    private function obtenerCodTipoActividad($obj, $nombre){
        // Se compara sin tildes ni mayusculas/minusculas (con TRANSLATE dentro
        // de PostgreSQL) para evitar errores de codificacion entre PHP y la
        // base de datos al escribir palabras con tilde (ej. "Inspeccion").
        $sql = "SELECT codtipoactividad
                FROM tbltipoactividadterreno
                WHERE UPPER(TRANSLATE(nombreactividad, 'ÁÉÍÓÚ', 'AEIOU')) = UPPER(:nombre)
                LIMIT 1";
        $resultado = $obj->select($sql, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC); 
        return $resultado ? $resultado['codtipoactividad'] : null;
    }

//solo aparecen sitios donde tanto el sitio como su tipo de depósito están activos
    
    private function obtenerDepositosActivos($obj){
        $sql = "SELECT s.codsitio AS id, s.nombresitio, td.nombretipodeposito AS tipodeposito
                FROM tblsitio s
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                WHERE s.estado = 'A' AND td.estado = 'A'
                ORDER BY s.nombresitio ASC";
        return $this->consultarSeguro($obj, $sql);
    }
//trae los datos del catálogo e incluye la vista
    public function Inspeccion(){
        $obj = new ActividadesTerModel();
        $depositos = $this->obtenerDepositosActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesTer/Inspeccion.php';
    }

    public function postCreateInspeccion(){

        $obj = new ActividadesTerModel();

        $depositoId = $_POST['deposito_id'] ?? null;
        $fecha      = $_POST['fecha_actividad'] ?? null;
        $hora       = $_POST['hora_actividad'] ?: null;
        $ph         = $_POST['ph'] !== '' ? $_POST['ph'] : null;
        $temperatura = $_POST['temperatura'] !== '' ? $_POST['temperatura'] : null;

        $larvasAedes = $_POST['larvas_aedes'] !== '' ? $_POST['larvas_aedes'] : 0;
        $pupas       = $_POST['pupas'] !== '' ? $_POST['pupas'] : 0;
        $larvasCulex = $_POST['larvas_culex'] !== '' ? $_POST['larvas_culex'] : 0;

        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($depositoId) || empty($fecha) || $ph === null || $temperatura === null){
            $_SESSION['error'] = "El depósito, la fecha, el pH y la temperatura son obligatorios.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!is_numeric($ph) || $ph < 0 || $ph > 14){
            $_SESSION['error'] = "El pH debe ser un número entre 0 y 14.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if(!is_numeric($temperatura) || $temperatura < 0 || $temperatura > 40){
            $_SESSION['error'] = "La temperatura debe ser un número entre 0°C y 40°C.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Inspeccion'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Inspeccion');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, ph, temperatura, larvasaedes, pupas, larvasculex, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :ph, :temperatura, :larvasaedes, :pupas, :larvasculex, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codsitio'         => $depositoId,
            ':codusuario'       => $_SESSION['usu_id'], 
            ':fecha'            => $fecha,
            ':hora'             => $hora,
            ':ph'               => $ph,
            ':temperatura'      => $temperatura,
            ':larvasaedes'      => $larvasAedes,
            ':pupas'            => $pupas,
            ':larvasculex'      => $larvasCulex,
            ':observaciones'    => $observaciones,
        ]);

        $nuevo = $obj->select(
            "SELECT codactividad FROM tblactividadterreno WHERE codusuario = :codusuario ORDER BY codactividad DESC LIMIT 1",
            [':codusuario' => $_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad'] ?? null,
            null,
            'Inspección — sitio #' . $depositoId
        );

        $_SESSION['exito'] = "La actividad de inspección se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    public function Siembra(){
        $obj = new ActividadesTerModel();
        $depositos = $this->obtenerDepositosActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesTer/Siembra.php';
    }

    public function postCreateSiembra(){

        $obj = new ActividadesTerModel();

        $depositoId  = $_POST['deposito_id'] ?? null;
        $fecha       = $_POST['fecha_actividad'] ?? null;
        $hora        = $_POST['hora_actividad'] ?: null;
        $hembras     = $_POST['cantidad_hembras'] !== '' ? $_POST['cantidad_hembras'] : 0;
        $machos      = $_POST['cantidad_machos'] !== '' ? $_POST['cantidad_machos'] : 0;
        $tiempoAclimatar = $_POST['tiempo_aclimatar'] !== '' ? $_POST['tiempo_aclimatar'] : 0;
        $volumenAgua = $_POST['volumen_agua'] !== '' ? $_POST['volumen_agua'] : 0;
        $recolectarEmpacar = $_POST['recolectar_empacar'] ?? 'N';
        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($depositoId) || empty($fecha)){
            $_SESSION['error'] = "El depósito y la fecha son obligatorios.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(!is_numeric($hembras) || !is_numeric($machos) || $hembras < 0 || $machos < 0){
            $_SESSION['error'] = "La cantidad de hembras y machos debe ser un número no negativo.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if(($hembras + $machos) <= 0){
            $_SESSION['error'] = "Debe registrar al menos un guppy (hembra o macho) en la siembra.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Siembra');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, cantidadhembras, cantidadmachos, tiempoaclimatacionmin, volumenagualitros, recolectarempacar, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :hembras, :machos, :tiempoaclimatacionmin, :volumenagualitros, :recolectarempacar, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad'      => $codTipo,
            ':codsitio'              => $depositoId,
            ':codusuario'            => $_SESSION['usu_id'],
            ':fecha'                 => $fecha,
            ':hora'                  => $hora,
            ':hembras'               => $hembras,
            ':machos'                => $machos,
            ':tiempoaclimatacionmin' => $tiempoAclimatar,
            ':volumenagualitros'     => $volumenAgua,
            ':recolectarempacar'     => $recolectarEmpacar,
            ':observaciones'         => $observaciones,
        ]);

        $nuevo = $obj->select(
            "SELECT codactividad FROM tblactividadterreno WHERE codusuario = :codusuario ORDER BY codactividad DESC LIMIT 1",
            [':codusuario' => $_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad'] ?? null,
            null,
            'Siembra — sitio #' . $depositoId
        );

        $_SESSION['exito'] = "La actividad de siembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    public function Seguimiento(){
        $obj = new ActividadesTerModel();
        $depositos = $this->obtenerDepositosActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesTer/Seguimiento.php';
    }

    public function postCreateSeguimiento(){

        $obj = new ActividadesTerModel();

        $depositoId = $_POST['deposito_id'] ?? null;
        $fecha      = $_POST['fecha_actividad'] ?? null;
        $hora       = $_POST['hora_actividad'] ?: null;
        $peces      = $_POST['peces'] ?? 'N';
        $larvas     = $_POST['larvas'] ?? 'N';
        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($depositoId) || empty($fecha)){
            $_SESSION['error'] = "El depósito y la fecha son obligatorios.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Seguimiento'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Seguimiento');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, peces, larvas, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :peces, :larvas, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad' => $codTipo,
            ':codsitio'         => $depositoId,
            ':codusuario'       => $_SESSION['usu_id'],
            ':fecha'            => $fecha,
            ':hora'             => $hora,
            ':peces'            => $peces,
            ':larvas'           => $larvas,
            ':observaciones'    => $observaciones,
        ]);

        $nuevo = $obj->select(
            "SELECT codactividad FROM tblactividadterreno WHERE codusuario = :codusuario ORDER BY codactividad DESC LIMIT 1",
            [':codusuario' => $_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad'] ?? null,
            null,
            'Seguimiento — sitio #' . $depositoId
        );

        $_SESSION['exito'] = "La actividad de seguimiento se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    public function Resiembra(){
        $obj = new ActividadesTerModel();
        $depositos = $this->obtenerDepositosActivos($obj);
        include_once __DIR__ . '/../../../view/ActividadesTer/Resiembra.php';
    }

    public function postCreateResiembra(){

        $obj = new ActividadesTerModel();

        $depositoId  = $_POST['deposito_id'] ?? null;
        $fecha       = $_POST['fecha_actividad'] ?? null;
        $hora        = $_POST['hora_actividad'] ?: null;
        $hembras     = $_POST['cantidad_hembras'] !== '' ? $_POST['cantidad_hembras'] : 0;
        $machos      = $_POST['cantidad_machos'] !== '' ? $_POST['cantidad_machos'] : 0;
        $tiempoAclimatar = $_POST['tiempo_aclimatar'] !== '' ? $_POST['tiempo_aclimatar'] : 0;
        $recolectarEmpacar = $_POST['recolectar_empacar'] ?? 'N';
        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($depositoId) || empty($fecha)){
            $_SESSION['error'] = "El depósito y la fecha son obligatorios.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(!is_numeric($hembras) || !is_numeric($machos) || $hembras < 0 || $machos < 0){
            $_SESSION['error'] = "La cantidad de hembras y machos debe ser un número no negativo.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if(($hembras + $machos) <= 0){
            $_SESSION['error'] = "Debe registrar al menos un guppy (hembra o macho) en la resiembra.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Resiembra'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Resiembra');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, cantidadhembras, cantidadmachos, tiempoaclimatacionmin, recolectarempacar, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :hembras, :machos, :tiempoaclimatacionmin, :recolectarempacar, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad'      => $codTipo,
            ':codsitio'              => $depositoId,
            ':codusuario'            => $_SESSION['usu_id'],
            ':fecha'                 => $fecha,
            ':hora'                  => $hora,
            ':hembras'               => $hembras,
            ':machos'                => $machos,
            ':tiempoaclimatacionmin' => $tiempoAclimatar,
            ':recolectarempacar'     => $recolectarEmpacar,
            ':observaciones'         => $observaciones,
        ]);

        $nuevo = $obj->select(
            "SELECT codactividad FROM tblactividadterreno WHERE codusuario = :codusuario ORDER BY codactividad DESC LIMIT 1",
            [':codusuario' => $_SESSION['usu_id']]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTer',
            $nuevo['codactividad'] ?? null,
            null,
            'Resiembra — sitio #' . $depositoId
        );

        $_SESSION['exito'] = "La actividad de resiembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    public function listMisActividades(){

        $obj = new ActividadesTerModel();

        $sql = "SELECT
                    a.codactividad AS id,
                    a.fecha,
                    t.nombreactividad AS tipo_actividad,
                    td.nombretipodeposito AS deposito,
                    s.nombresitio AS sitio,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s ON s.codsitio = a.codsitio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito  
                WHERE a.codusuario = :codusuario
                ORDER BY a.fecha DESC, a.codactividad DESC";

        $actividades = $this->consultarSeguro($obj, $sql, [':codusuario' => $_SESSION['usu_id']]);

        $depositos = $this->obtenerDepositosActivos($obj);

        include_once __DIR__ . '/../../../view/ActividadesTer/listMisActividades.php';

    }

    public function listActTer(){

        $obj = new ActividadesTerModel();

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
            $condiciones[] = "s.codsitio = :codsitio";
            $parametros[':codsitio'] = $codSitio;
        }
        if(!empty($codTipoActividad)){
            $condiciones[] = "a.codtipoactividad = :codtipoactividad";
            $parametros[':codtipoactividad'] = $codTipoActividad;
        }

        $where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

        $sql = "SELECT a.codactividad,
                       a.fecha,
                       t.nombreactividad AS tipo_actividad,
                       s.nombresitio AS sitio,
                       (u.nombreusuario || ' ' || u.apellidousuario) AS responsable,
                       a.observaciones,
                       a.estado
                FROM tblactividadterreno a
                JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                JOIN tblsitio s ON s.codsitio = a.codsitio
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

        include_once __DIR__ . '/../../../view/ActividadesTer/listActTer.php';

    }

    public function filtro(){

        $obj = new ActividadesTerModel();
        $mes = $_POST['mes'] ?: null;
        $mesInicio = null;
        $mesFin = null;
        if ($mes) {
            $mesInicio = $mes . '-01';
            $mesFin = date('Y-m-d', strtotime($mesInicio . ' +1 month'));
        }

        $coddeposito   = $_POST['coddeposito'] ?: null;
        $tipoActividad = $_POST['tipoactividad'] ?: null;

        $sql = "SELECT
                    a.codactividad AS id,
                    a.fecha,
                    t.nombreactividad AS tipo_actividad,
                    td.nombretipodeposito AS deposito,
                    s.nombresitio AS sitio,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s ON s.codsitio = a.codsitio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                WHERE a.codusuario = :codusuario
                    AND (:mesInicio::date IS NULL OR (a.fecha >= :mesInicio::date AND a.fecha < :mesFin::date))
                    AND (:coddeposito::integer IS NULL OR a.codsitio = :coddeposito::integer)
                    AND (:tipoactividad::text IS NULL OR t.nombreactividad ILIKE :tipoactividad::text)
                ORDER BY a.fecha DESC, a.codactividad DESC";

        $actividades = $this->consultarSeguro($obj, $sql, [
            ':codusuario'     => $_SESSION['usu_id'],
            ':mesInicio'      => $mesInicio,
            ':mesFin'         => $mesFin,
            ':coddeposito'    => $coddeposito,
            ':tipoactividad'  => $tipoActividad,
        ]);

        include_once __DIR__ . '/../../../view/ActividadesTer/filtroMisActividades.php';

    }

    public function getUpdate(){

        $obj = new ActividadesTerModel();

        $id = $_GET['id'];

        $sql = "SELECT a.*, t.nombreactividad
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                WHERE a.codactividad = :id";

        $actividad = $obj->select($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$actividad || $actividad['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $depositos = $this->obtenerDepositosActivos($obj);

        include_once __DIR__ . '/../../../view/ActividadesTer/getUpdateAct.php';

    }

    public function postUpdate(){

        $obj = new ActividadesTerModel();

        $codactividad = $_POST['codactividad'] ?? null;

        $sqlActual = "SELECT * FROM tblactividadterreno WHERE codactividad = :id";
        $actual = $obj->select($sqlActual, [':id' => $codactividad])->fetch(PDO::FETCH_ASSOC);

        if(!$actual || $actual['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para editar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $fecha  = $_POST['fecha_actividad'] ?? null;
        $hora   = $_POST['hora_actividad'] ?: null;
        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($fecha)){
            $_SESSION['error'] = "La fecha es obligatoria.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if($fecha < '2026-09-10' || $fecha > date('Y-m-d')){
            $_SESSION['error'] = "La fecha debe estar entre el 10 de septiembre de 2026 y hoy.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        // Mismos rangos logicos que en la creacion; este endpoint es
        // compartido por los 4 tipos de actividad de terreno.
        if(isset($_POST['ph']) && $_POST['ph'] !== '' && (!is_numeric($_POST['ph']) || $_POST['ph'] < 0 || $_POST['ph'] > 14)){
            $_SESSION['error'] = "El pH debe ser un número entre 0 y 14.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if(isset($_POST['temperatura']) && $_POST['temperatura'] !== '' && (!is_numeric($_POST['temperatura']) || $_POST['temperatura'] < 0 || $_POST['temperatura'] > 40)){
            $_SESSION['error'] = "La temperatura debe ser un número entre 0°C y 40°C.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        if((isset($_POST['cantidad_hembras']) && $_POST['cantidad_hembras'] !== '' && (!is_numeric($_POST['cantidad_hembras']) || $_POST['cantidad_hembras'] < 0))
           || (isset($_POST['cantidad_machos']) && $_POST['cantidad_machos'] !== '' && (!is_numeric($_POST['cantidad_machos']) || $_POST['cantidad_machos'] < 0))){
            $_SESSION['error'] = "La cantidad de hembras y machos debe ser un número no negativo.";
            redirect(getUrl('ActividadesTer','ActividadesTer','getUpdate',['id'=>$codactividad]));
            exit();
        }

        $sql = "UPDATE tblactividadterreno SET
                    fecha = :fecha,
                    hora = :hora,
                    ph = :ph,
                    temperatura = :temperatura,
                    larvasaedes = :larvasaedes,
                    pupas = :pupas,
                    larvasculex = :larvasculex,
                    larvas = :larvas,
                    peces = :peces,
                    cantidadhembras = :cantidadhembras,
                    cantidadmachos = :cantidadmachos,
                    tiempoaclimatacionmin = :tiempoaclimatacionmin,
                    recolectarempacar = :recolectarempacar,
                    observaciones = :observaciones
                WHERE codactividad = :codactividad";

        $obj->update($sql, [
            ':fecha'                 => $fecha,
            ':hora'                  => $hora,
            ':ph'                    => (isset($_POST['ph']) && $_POST['ph'] !== '') ? $_POST['ph'] : $actual['ph'],
            ':temperatura'           => (isset($_POST['temperatura']) && $_POST['temperatura'] !== '') ? $_POST['temperatura'] : $actual['temperatura'],
            ':larvasaedes'           => (isset($_POST['larvas_aedes']) && $_POST['larvas_aedes'] !== '') ? $_POST['larvas_aedes'] : $actual['larvasaedes'],
            ':pupas'                 => (isset($_POST['pupas']) && $_POST['pupas'] !== '') ? $_POST['pupas'] : $actual['pupas'],
            ':larvasculex'           => (isset($_POST['larvas_culex']) && $_POST['larvas_culex'] !== '') ? $_POST['larvas_culex'] : $actual['larvasculex'],
            ':larvas'                => $_POST['larvas'] ?? $actual['larvas'],
            ':peces'                 => $_POST['peces'] ?? $actual['peces'],
            ':cantidadhembras'       => (isset($_POST['cantidad_hembras']) && $_POST['cantidad_hembras'] !== '') ? $_POST['cantidad_hembras'] : $actual['cantidadhembras'],
            ':cantidadmachos'        => (isset($_POST['cantidad_machos']) && $_POST['cantidad_machos'] !== '') ? $_POST['cantidad_machos'] : $actual['cantidadmachos'],
            ':tiempoaclimatacionmin' => (isset($_POST['tiempo_aclimatar']) && $_POST['tiempo_aclimatar'] !== '') ? $_POST['tiempo_aclimatar'] : $actual['tiempoaclimatacionmin'],
            ':recolectarempacar'     => $_POST['recolectar_empacar'] ?? $actual['recolectarempacar'],
            ':observaciones'         => $observaciones,
            ':codactividad'          => $codactividad,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesTer',
            $codactividad,
            null,
            'Actividad #' . $codactividad . ' editada'
        );

        $_SESSION['exito'] = "El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    public function delete(){

        $obj = new ActividadesTerModel();

        $id  = $_GET['id'] ?? null;
        $rol = $_SESSION['nombre_rol'] ?? '';

        $funcionRetorno = ($rol === 'Coordinador Control Biologico') ? 'listActTer' : 'listMisActividades';

        if(empty($id)){
            $_SESSION['error'] = "Registro no válido.";
            redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
            exit();
        }

        $actual = $obj->select("SELECT estado, codusuario FROM tblactividadterreno WHERE codactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        $esPropietario = $actual && $actual['codusuario'] == $_SESSION['usu_id'];
        $esCoordinador = ($rol === 'Coordinador Control Biologico');

        if(!$actual || (!$esPropietario && !$esCoordinador)){
            $_SESSION['error'] = "No tiene permisos para modificar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
            exit();
        }

        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblactividadterreno SET estado = :estado WHERE codactividad = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesTer',
            $id,
            $actual['estado'] === 'A' ? 'Activo' : 'Inactivo',
            $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
        );

        $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
        exit();

    }

}