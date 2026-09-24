<?php
/*
 * =====================================================================
 *  Permisos por MÓDULO
 *
 *  - Un módulo es una carpeta de src/Controller (el ?modulo= de la URL).
 *  - Un rol tiene el módulo completo o no lo tiene (tabla tblrolmodulo).
 *  - La barra lateral se arma con las mismas tablas, así que lo que no
 *    tiene permiso no aparece en el menú NI se puede abrir por URL.
 *  - Los permisos se leen de la BD en cada petición (no se guardan en la
 *    sesión), por eso un cambio aplica de inmediato, sin cerrar sesión.
 * =====================================================================
 */

<<<<<<< Updated upstream
// Se pueden usar sin iniciar sesión (login y recuperar contraseña).
const MODULOS_SIN_SESION = ['acceso', 'cambiocontra'];

// Cualquier usuario con sesión iniciada los puede usar, sin importar su rol.
const MODULOS_COMUNES = ['index', 'inicio', 'perfil', 'sobrenosotros'];
=======
$GLOBALS['MODULOS_PUBLICOS'] = ['Acceso', 'CambioContra', 'index', 'SobreNosotros', 'Inicio', 'Perfil', 'Manuales'];  
>>>>>>> Stashed changes


function haySesionActiva()
{
    return isset($_SESSION['auth']) && $_SESSION['auth'] === 'ok' && !empty($_SESSION['codrol']);
}


/**
 * Carpetas (en minúscula) de los módulos a los que tiene acceso el rol.
 * Solo cuenta módulos activos y roles activos.
 * Se guarda en memoria durante la petición para no consultar varias veces.
 */
function modulosPermitidosDelRol($codrol)
{
    static $cache = [];

    if (empty($codrol)) {
        return [];
    }

    if (isset($cache[$codrol])) {
        return $cache[$codrol];
    }

    try {
        $obj = new \BioGuppy\Model\MasterModel();

        $sql = "SELECT LOWER(m.carpeta) AS carpeta
                FROM tblrolmodulo rm
                INNER JOIN tblmodulo m ON m.codmodulo = rm.codmodulo
                INNER JOIN tblrol    r ON r.codrol    = rm.codrol
                WHERE rm.codrol = :codrol
                  AND m.estado = 'A'
                  AND r.estado = 'A'";

        $cache[$codrol] = $obj->select($sql, [':codrol' => $codrol])->fetchAll(\PDO::FETCH_COLUMN);

    } catch (\Throwable $error) {
        error_log("Error consultando permisos del rol: " . $error->getMessage());
        $cache[$codrol] = [];
    }

    return $cache[$codrol];
}


/**
 * ¿El usuario en sesión puede entrar a este módulo?
 * $controlador y $funcion se reciben por compatibilidad, pero ya no se usan:
 * el permiso es para TODO el módulo.
 */
function usuarioTienePermiso($modulo, $controlador = null, $funcion = null)
{
    $modulo = strtolower(trim((string) $modulo));

    if ($modulo === '') {
        return false;
    }

    if (in_array($modulo, MODULOS_SIN_SESION, true)) {
        return true;
    }

    if (!haySesionActiva()) {
        return false;
    }

    if (in_array($modulo, MODULOS_COMUNES, true)) {
        return true;
    }

    return in_array($modulo, modulosPermitidosDelRol($_SESSION['codrol']), true);
}


/**
 * Menú de la barra lateral para un rol, agrupado por sección:
 *
 * [
 *   'Seguridad' => [
 *      ['codmodulo'=>1, 'nombremodulo'=>'Usuarios', 'carpeta'=>'Usuarios', 'icono'=>'bi-people-fill',
 *       'enlaces' => [ ['texto'=>'Usuarios', 'controlador'=>'Usuarios', 'funcion'=>'listUsu'] ]],
 *   ],
 *   ...
 * ]
 */
function menuDelRol($codrol)
{
    if (empty($codrol)) {
        return [];
    }

    try {
        $obj = new \BioGuppy\Model\MasterModel();

        $sql = "SELECT m.codmodulo, m.nombremodulo, m.carpeta, m.icono, m.seccion,
                       e.texto, e.controlador, e.funcion
                FROM tblrolmodulo rm
                INNER JOIN tblmodulo       m ON m.codmodulo = rm.codmodulo
                INNER JOIN tblrol          r ON r.codrol    = rm.codrol
                INNER JOIN tblmoduloenlace e ON e.codmodulo = m.codmodulo
                WHERE rm.codrol = :codrol
                  AND m.estado = 'A'
                  AND r.estado = 'A'
                ORDER BY m.orden, m.nombremodulo, e.orden, e.codenlace";

        $filas = $obj->select($sql, [':codrol' => $codrol])->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\Throwable $error) {
        error_log("Error armando el menú del rol: " . $error->getMessage());
        return [];
    }

    $menu = [];
    foreach ($filas as $fila) {
        $seccion = $fila['seccion'];
        $cod     = $fila['codmodulo'];

        if (!isset($menu[$seccion][$cod])) {
            $menu[$seccion][$cod] = [
                'codmodulo'    => $cod,
                'nombremodulo' => $fila['nombremodulo'],
                'carpeta'      => $fila['carpeta'],
                'icono'        => $fila['icono'],
                'enlaces'      => [],
            ];
        }

        $menu[$seccion][$cod]['enlaces'][] = [
            'texto'       => $fila['texto'],
            'controlador' => $fila['controlador'],
            'funcion'     => $fila['funcion'],
        ];
    }

    return $menu;
}


/**
 * Revisa el permiso de la ruta actual ANTES de pintar la página.
 * Si no tiene permiso lo manda al login (sin sesión) o al inicio (con sesión).
 */
function verificarAccesoRuta($modulo)
{
    if (usuarioTienePermiso($modulo)) {
        return;
    }

    $esAjax = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'ajax.php';

    if (!haySesionActiva()) {
        if ($esAjax) {
            http_response_code(401);
            echo '<div class="alert alert-warning mb-0">Tu sesión terminó. Vuelve a iniciar sesión.</div>';
        } else {
            irA('inicio/login.php');
        }
        exit();
    }

    if ($esAjax) {
        http_response_code(403);
        echo '<div class="alert alert-danger mb-0">Tu rol no tiene acceso a este módulo.</div>';
        exit();
    }

    $_SESSION['acceso_denegado'] = 'Tu rol no tiene acceso a ese módulo.';
    irA(getUrl('Inicio', 'Inicio', 'index'));
    exit();
}


// Redirección real por cabecera si todavía no se ha impreso nada; si no, por JS.
function irA($url)
{
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        redirect($url);
    }
}
