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
        $sql = "SELECT codtipoactividad FROM tbltipoactividadterreno WHERE nombreactividad ILIKE :nombre LIMIT 1";
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
//
    public function postCreateInspeccion(){

        $obj = new ActividadesTerModel();

        $depositoId = $_POST['deposito_id'] ?? null; // ??: operador de fusion null, es para campos donde no existe un valor
        $fecha      = $_POST['fecha_actividad'] ?? null;
        $hora       = $_POST['hora_actividad'] ?: null; //?: es parecido solo que muestra que el valor xiste pero esta vacio, muestra en este caso una cadena de texto vacia no que no existe como ??
        $ph         = $_POST['ph'] !== '' ? $_POST['ph'] : null;
        $temperatura = $_POST['temperatura'] !== '' ? $_POST['temperatura'] : null;

        // lo mismo, solo que en vez de null ponemos 0 para la cantidad

        $larvasAedes = $_POST['larvas_aedes'] !== '' ? $_POST['larvas_aedes'] : 0;
        $pupas       = $_POST['pupas'] !== '' ? $_POST['pupas'] : 0;
        $larvasCulex = $_POST['larvas_culex'] !== '' ? $_POST['larvas_culex'] : 0;

        $observaciones = $_POST['observaciones'] ?? '';

        // Validando campos obligatorios, solo 2 deposito y fecha.
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
            ':codusuario'       => $_SESSION['usu_id'], //este es el dato para saber "quién" registró la actividad, se guardó ahí al iniciar sesión
            ':fecha'            => $fecha,
            ':hora'             => $hora,
            ':ph'               => $ph,
            ':temperatura'      => $temperatura,
            ':larvasaedes'      => $larvasAedes,
            ':pupas'            => $pupas,
            ':larvasculex'      => $larvasCulex,
            ':observaciones'    => $observaciones,
        ]);

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadterreno")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesTer', $nuevoId['id'] ?? null, null, $codTipo);

        $_SESSION['exito'] = "La actividad de inspección se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

//
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

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadterreno")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesTer', $nuevoId['id'] ?? null, null, $codTipo);

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

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadterreno")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesTer', $nuevoId['id'] ?? null, null, $codTipo);

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

        $nuevoId = $obj->select("SELECT MAX(codactividad) AS id FROM tblactividadterreno")->fetch(PDO::FETCH_ASSOC);
        $this->registrarBitacora($obj, 'INSERT', 'ActividadesTer', $nuevoId['id'] ?? null, null, $codTipo);

        $_SESSION['exito'] = "La actividad de resiembra se registró exitosamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }

// 
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
//es para mostrar estos 3 datos: actividad, sitio y tipo de depósito del sitio en una fila
        $actividades = $this->consultarSeguro($obj, $sql, [':codusuario' => $_SESSION['usu_id']]);

        // Se cargan tambien los depositos activos, para el combo de filtro
        $depositos = $this->obtenerDepositosActivos($obj);
// aqui se llena el select de Deposito ene l formulario del filtro
        include_once __DIR__ . '/../../../view/ActividadesTer/listMisActividades.php';

    }

// Historial de actividades de terreno (vista del Coordinador, muestra TODAS las actividades de TODOS los usuarios)
    public function listActTer(){

        $obj = new ActividadesTerModel();

        $fechaDesde       = $_GET['fechaDesde'] ?? '';
        $fechaHasta       = $_GET['fechaHasta'] ?? '';
        $codSitio         = $_GET['codsitio'] ?? '';
        $codTipoActividad = $_GET['codtipoactividad'] ?? '';

        $condiciones = [];
        $parametros  = [];

        if(!empty($fechaDesde)){
            $condiciones[] = "a.fecha >= :fechaDesde";
            $parametros[':fechaDesde'] = $fechaDesde;
        }
        if(!empty($fechaHasta)){
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
            $mesInicio = $mes . '-01';                                   // "2026-09" -> "2026-09-01"
            $mesFin = date('Y-m-d', strtotime($mesInicio . ' +1 month')); // -> "2026-10-01"
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

//
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

// Se actualizan todos los campos posibles, los que no aplican al tipo de actividad simplemente llegan vacios/null desde el form
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

        $this->registrarBitacora($obj, 'UPDATE', 'ActividadesTer', $codactividad, null, $fecha);

        $_SESSION['exito'] = "El registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer','listMisActividades'));
        exit();

    }
    public function delete(){

        $obj = new ActividadesTerModel();

        $id  = $_GET['id'] ?? null;
        $rol = $_SESSION['nombre_rol'] ?? '';

        // El Coordinador ve/gestiona el historial completo (listActTer);
        // el Auxiliar solo ve y gestiona sus propias actividades (listMisActividades)
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

        $this->registrarBitacora($obj, 'UPDATE', 'ActividadesTer', $id, $actual['estado'] ?? null, $nuevoEstado);

        $_SESSION['exito'] = "El estado del registro se actualizó correctamente.";
        redirect(getUrl('ActividadesTer','ActividadesTer',$funcionRetorno));
        exit();

    }

}