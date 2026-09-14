<?php

namespace BioGuppy\Controller\ActividadesTer;

use BioGuppy\Model\ActividadesTer\ActividadesTerModel;
use PDO;

class ActividadesTerController{

    // -----------------------------------------------------------------
    // HELPER: version seguro de select() para consultas de SOLO LECTURA
    // (listar, llenar combos). Si la consulta falla (tabla/columna que
    // no existe, etc.), en vez de dejar que el error rompa toda la
    // pagina, devuelve "false" -- la vista ya sabe mostrar su estado
    // normal de "no hay datos" con eso (mismo comportamiento que si la
    // tabla existiera pero estuviera vacia). El detalle tecnico queda
    // en el log del servidor. Las consultas de escritura (insert/update)
    // NO usan este helper a proposito: si guardar falla, el usuario
    // debe enterarse, no se le puede hacer creer que si se guardo.
    // -----------------------------------------------------------------
    private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en ActividadesTerController: " . $error->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // HELPER: busca el codigo de un tipo de actividad de terreno por su
    // nombre (Inspeccion / Siembra / Seguimiento / Resiembra) en el
    // catalogo tbltipoactividadterreno. Se hace por nombre (no por un
    // ID fijo) porque el ID real depende de como haya quedado sembrada
    // la tabla en cada instalacion de la BD.
    // -----------------------------------------------------------------
    private function obtenerCodTipoActividad($obj, $nombre){
        $sql = "SELECT codtipoactividad FROM tbltipoactividadterreno WHERE nombreactividad ILIKE :nombre LIMIT 1";
        $resultado = $obj->select($sql, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['codtipoactividad'] : null;
    }

    // -----------------------------------------------------------------
    // HELPER: trae los depositos disponibles para el combo "Deposito" de
    // los 4 formularios de registro. Solo se muestran sitios ACTIVOS
    // (s.estado = 'A') Y cuyo tipo de deposito tambien este ACTIVO
    // (td.estado = 'A'): si el Coordinador inhabilita el sitio, o el
    // Auxiliar inhabilita el tipo de deposito desde el catalogo, ese
    // sitio deja de poder elegirse para registrar actividades NUEVAS
    // -- pero las actividades YA registradas con ese tipo/sitio NO se
    // tocan, se mantiene el historial tal cual quedo (por eso "Mis
    // actividades" sigue mostrando el nombre del deposito de registros
    // viejos aunque el tipo ya este inhabilitado).
    //
    // Es una consulta de SOLO LECTURA (llena un combo): usa
    // consultarSeguro(), asi que si tblsitio o tbltipodeposito no
    // existen todavia, el combo simplemente sale vacio en vez de
    // tumbar la pagina completa.
    // -----------------------------------------------------------------
    private function obtenerDepositosActivos($obj){
        $sql = "SELECT s.codsitio AS id, s.nombresitio, td.nombredeposito AS tipodeposito
                FROM tblsitio s
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                WHERE s.estado = 'A' AND td.estado = 'A'
                ORDER BY s.nombresitio ASC";
        return $this->consultarSeguro($obj, $sql);
    }

    // ===================================================================
    // INSPECCION (RF023)
    // ===================================================================

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

        // Validando campos obligatorios
        if(empty($depositoId) || empty($fecha)){
            $_SESSION['error'] = "El depósito y la fecha son obligatorios.";
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

        $_SESSION['exito'] = "La actividad de inspección se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    // ===================================================================
    // SIEMBRA (RF024)
    // ===================================================================

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
        $recolectarEmpacar = $_POST['recolectar_empacar'] ?? 'N';
        $observaciones = $_POST['observaciones'] ?? '';

        if(empty($depositoId) || empty($fecha)){
            $_SESSION['error'] = "El depósito y la fecha son obligatorios.";
            redirect(getUrl('ActividadesTer','ActividadesTer','Siembra'));
            exit();
        }

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Siembra');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, cantidadhembras, cantidadmachos, tiempoaclimatar, recolectarempacar, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :hembras, :machos, :tiempoaclimatar, :recolectarempacar, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad'   => $codTipo,
            ':codsitio'           => $depositoId,
            ':codusuario'         => $_SESSION['usu_id'],
            ':fecha'              => $fecha,
            ':hora'               => $hora,
            ':hembras'            => $hembras,
            ':machos'             => $machos,
            ':tiempoaclimatar'    => $tiempoAclimatar,
            ':recolectarempacar'  => $recolectarEmpacar,
            ':observaciones'      => $observaciones,
        ]);

        $_SESSION['exito'] = "La actividad de siembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    // ===================================================================
    // SEGUIMIENTO (RF025)
    // ===================================================================

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

        $_SESSION['exito'] = "La actividad de seguimiento se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    // ===================================================================
    // RESIEMBRA (RF026)
    // ===================================================================

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

        $codTipo = $this->obtenerCodTipoActividad($obj, 'Resiembra');

        $sql = "INSERT INTO public.tblactividadterreno
                    (codactividad, codtipoactividad, codsitio, codusuario, fecha, hora, cantidadhembras, cantidadmachos, tiempoaclimatar, recolectarempacar, observaciones, fechacreacion, estado)
                VALUES
                    (DEFAULT, :codtipoactividad, :codsitio, :codusuario, :fecha, :hora, :hembras, :machos, :tiempoaclimatar, :recolectarempacar, :observaciones, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codtipoactividad'   => $codTipo,
            ':codsitio'           => $depositoId,
            ':codusuario'         => $_SESSION['usu_id'],
            ':fecha'              => $fecha,
            ':hora'               => $hora,
            ':hembras'            => $hembras,
            ':machos'             => $machos,
            ':tiempoaclimatar'    => $tiempoAclimatar,
            ':recolectarempacar'  => $recolectarEmpacar,
            ':observaciones'      => $observaciones,
        ]);

        $_SESSION['exito'] = "La actividad de resiembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    // ===================================================================
    // MIS ACTIVIDADES (RF027 / consulta propia del auxiliar) — solo
    // muestra lo que el usuario autenticado ha registrado.
    // ===================================================================

    public function listMisActividades(){

        $obj = new ActividadesTerModel();

        $sql = "SELECT
                    a.codactividad AS id,
                    a.fecha,
                    t.nombreactividad AS tipo_actividad,
                    td.nombredeposito AS deposito,
                    s.nombresitio AS sitio,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s ON s.codsitio = a.codsitio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                WHERE a.codusuario = :codusuario
                ORDER BY a.fecha DESC, a.codactividad DESC";

        $actividades = $this->consultarSeguro($obj, $sql, [':codusuario' => $_SESSION['usu_id']]);

        // Se cargan tambien los depositos activos, para el combo de filtro
        $depositos = $this->obtenerDepositosActivos($obj);

        include_once __DIR__ . '/../../../view/ActividadesTer/listMisActividades.php';

    }

    // -----------------------------------------------------------------
    // BUSCADOR (ajax) de "Mis actividades": filtra por rango de fecha,
    // deposito y tipo de actividad, siempre limitado al propio usuario.
    // -----------------------------------------------------------------
    public function filtro(){

        $obj = new ActividadesTerModel();

        // "mes" llega del <input type="month"> como texto "YYYY-MM" (o
        // vacio si no se eligio). A partir de ahi calculamos el primer
        // dia de ese mes y el primer dia del mes SIGUIENTE, para poder
        // filtrar con un rango simple (fecha >= inicio AND fecha < fin)
        // -mas eficiente para Postgres que comparar mes a mes con texto.
        $mes = $_POST['mes'] ?: null;
        $mesInicio = null;
        $mesFin = null;
        if ($mes) {
            $mesInicio = $mes . '-01';                                   // "2026-09" -> "2026-09-01"
            $mesFin = date('Y-m-d', strtotime($mesInicio . ' +1 month')); // -> "2026-10-01"
        }

        $coddeposito   = $_POST['coddeposito'] ?: null;
        $tipoActividad = $_POST['tipoactividad'] ?: null;

        $sql = "SELECT
                    a.codactividad AS id,
                    a.fecha,
                    t.nombreactividad AS tipo_actividad,
                    td.nombredeposito AS deposito,
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

    // ===================================================================
    // EDITAR (RF027): el auxiliar solo puede editar actividades que el
    // mismo registro (se valida comparando codusuario contra la sesion).
    // ===================================================================

    public function getUpdate(){

        $obj = new ActividadesTerModel();

        $id = $_GET['id'];

        $sql = "SELECT a.*, t.nombreactividad
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno t ON t.codtipoactividad = a.codtipoactividad
                WHERE a.codactividad = :id";

        $actividad = $obj->select($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        // No permitir editar un registro que no le pertenece al usuario
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

        // Se vuelve a validar la propiedad del registro antes de actualizar
        $sqlDueno = "SELECT codusuario FROM tblactividadterreno WHERE codactividad = :id";
        $dueno = $obj->select($sqlDueno, [':id' => $codactividad])->fetch(PDO::FETCH_ASSOC);

        if(!$dueno || $dueno['codusuario'] != $_SESSION['usu_id']){
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

        // Se actualizan todos los campos posibles; los que no aplican al
        // tipo de actividad simplemente llegan vacios/null desde el form.
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
                    tiempoaclimatar = :tiempoaclimatar,
                    recolectarempacar = :recolectarempacar,
                    observaciones = :observaciones
                WHERE codactividad = :codactividad";

        $obj->update($sql, [
            ':fecha'             => $fecha,
            ':hora'              => $hora,
            ':ph'                => $_POST['ph'] !== '' ? $_POST['ph'] : null,
            ':temperatura'       => $_POST['temperatura'] !== '' ? $_POST['temperatura'] : null,
            ':larvasaedes'       => $_POST['larvas_aedes'] !== '' ? $_POST['larvas_aedes'] : null,
            ':pupas'             => $_POST['pupas'] !== '' ? $_POST['pupas'] : null,
            ':larvasculex'       => $_POST['larvas_culex'] !== '' ? $_POST['larvas_culex'] : null,
            ':larvas'            => $_POST['larvas'] ?? null,
            ':peces'             => $_POST['peces'] ?? null,
            ':cantidadhembras'   => $_POST['cantidad_hembras'] !== '' ? $_POST['cantidad_hembras'] : null,
            ':cantidadmachos'    => $_POST['cantidad_machos'] !== '' ? $_POST['cantidad_machos'] : null,
            ':tiempoaclimatar'   => $_POST['tiempo_aclimatar'] !== '' ? $_POST['tiempo_aclimatar'] : null,
            ':recolectarempacar' => $_POST['recolectar_empacar'] ?? null,
            ':observaciones'     => $observaciones,
            ':codactividad'      => $codactividad,
        ]);

        $_SESSION['exito'] = "El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

    // -----------------------------------------------------------------
    // INHABILITAR / HABILITAR (mismo patron rojo/verde que Depositos y
    // Usuarios). Igual que en postUpdate(), se verifica que el registro
    // sea del usuario en sesion antes de tocarlo.
    // -----------------------------------------------------------------
    public function delete(){

        $obj = new ActividadesTerModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Registro no válido.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $actual = $obj->select("SELECT estado, codusuario FROM tblactividadterreno WHERE codactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$actual || $actual['codusuario'] != $_SESSION['usu_id']){
            $_SESSION['error'] = "No tiene permisos para modificar este registro.";
            redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
            exit();
        }

        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblactividadterreno SET estado = :estado WHERE codactividad = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

}
