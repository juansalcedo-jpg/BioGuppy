<?php

/**
 * Control de acceso por rol — CONECTADO A BASE DE DATOS.
 *
 * Cada rol tiene, en tblrolaccion, la lista de acciones (Ver/Crear/Editar/
 * Inhabilitar) que puede hacer sobre cada "Modulo:Controlador" — eso lo
 * administra el Super Admin desde Roles > Permisos.
 *
 * Esta función decide, para cada request, a qué "acción" corresponde la
 * función que se está pidiendo (según su nombre), y consulta si el rol
 * actual tiene esa acción habilitada para ese módulo.
 */

// Módulos que deben poder usarse SIN haber iniciado sesión todavía
// (login y recuperación de contraseña).
$GLOBALS['MODULOS_PUBLICOS'] = ['Acceso', 'CambioContra'];


// Traduce el nombre de una función del controlador a una de las 4
// acciones genéricas que se administran en Roles > Permisos.
function clasificarAccionPorFuncion($funcion)
{
    $f = strtolower($funcion);

    if (strpos($f, 'delete') !== false || $f === 'activacion') {
        return 'Inhabilitar';
    }

    if (strpos($f, 'update') !== false) {
        return 'Editar';
    }

    if (strpos($f, 'create') !== false) {
        return 'Crear';
    }

    // Todo lo demás (listar, filtrar, ver detalle, generar reportes, etc.)
    return 'Ver';
}


function usuarioTienePermiso($modulo, $controlador, $funcion)
{

    if (in_array($modulo, $GLOBALS['MODULOS_PUBLICOS'] ?? [])) {
        return true;
    }

    $codrol = $_SESSION['codrol'] ?? null;

    if (empty($codrol)) {
        return false;
    }

    $par = "$modulo:$controlador";
    $accionNecesaria = clasificarAccionPorFuncion($funcion);

    try {

        $obj = new \BioGuppy\Model\MasterModel();

        $sql = "SELECT 1
                FROM tblrolaccion ra
                INNER JOIN tblaccion a ON a.codaccion = ra.codaccion
                INNER JOIN tblmodulo m ON m.codmodulo = a.codmodulo
                INNER JOIN tblpermiso p ON p.codpermiso = a.codpermiso
                WHERE ra.codrol = :codrol
                  AND m.nombremodulo = :par
                  AND p.nombrepermiso = :accion
                LIMIT 1";

        $stmt = $obj->select($sql, [
            ':codrol' => $codrol,
            ':par' => $par,
            ':accion' => $accionNecesaria,
        ]);

        return (bool) $stmt->fetchColumn();

    } catch (\Throwable $error) {
        error_log("Error verificando permisos: " . $error->getMessage());
        return false;
    }

}