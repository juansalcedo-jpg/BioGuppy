<?php

/**
 * Mapa de permisos por rol.
 *
 * 'pares': lista de "Modulo:Controlador" que ese rol puede usar COMPLETO
 *          (todas las funciones actuales y futuras de ese controlador:
 *          listar, crear, editar, activar/inhabilitar, etc.).
 *
 * 'funciones_por_par': para los pares que se COMPARTEN entre roles con
 *          funciones distintas (ej: ActividadesTer lo usa el Coordinador
 *          para ver el historial, y el Auxiliar Terreno para registrar),
 *          aquí se restringe a la lista exacta de funciones permitidas.
 */
$GLOBALS['PERMISOS_POR_ROL'] = [

    'Super Admin' => [
        'pares' => ['Usuarios:Usuarios', 'Roles:Roles', 'Auditoria:Auditoria', 'Parametros:Parametros'],
    ],

    'Administrador' => [
        'pares' => ['Dashboard:Dashboard', 'Catalogos:Catalogos', 'Catalogos:TipoTanque', 'Catalogos:TipoDeposito', 'Catalogos:ActividadesZoo', 'Catalogos:ActividadesTerre'],
    ],

    'Coordinador Control Biologico' => [
        'pares' => [
            'Zoocriadero:Zoocriadero',
            'Tanques:Tanques',
            'Sitios:Sitios',
            'Depositos:Depositos',
            'HistorialZoo:HistorialZoo',
            'ReportesZoo:ReportesZoo',
            'ReportesTer:ReportesTer',
        ],
        'funciones_por_par' => [
            'ActividadesTer:ActividadesTer' => ['listActTer'],
        ],
    ],

    'Auxiliar Zoocriadero' => [
        'pares' => [
            'ActividadesListZoo:ActividadesListZoo',
            'ActividadesZoo:Alimentacion',
            'ActividadesZoo:NacidosMuertos',
            'ActividadesZoo:Parametros',
            'ActividadesZoo:Limpieza',
            'ActividadesZoo:AjusteNivel',
            'ActividadesZoo:Lavado',
        ],
    ],

    'Auxiliar Terreno' => [
        'pares' => [],
        'funciones_por_par' => [
            'ActividadesTer:ActividadesTer' => ['listMisActividades', 'Inspeccion', 'Siembra', 'Seguimiento', 'Resiembra'],
        ],
    ],

];

// Módulos que deben poder usarse SIN haber iniciado sesión todavía
// (login y recuperación de contraseña).
$GLOBALS['MODULOS_PUBLICOS'] = ['Acceso', 'CambioContra'];

function usuarioTienePermiso($modulo, $controlador, $funcion)
{

    if (in_array($modulo, $GLOBALS['MODULOS_PUBLICOS'])) {
        return true;
    }

    $rol = $_SESSION['nombre_rol'] ?? null;

    if (!$rol || !isset($GLOBALS['PERMISOS_POR_ROL'][$rol])) {
        return false;
    }

    $permisosRol = $GLOBALS['PERMISOS_POR_ROL'][$rol];
    $par = "$modulo:$controlador";

    if (in_array($par, $permisosRol['pares'] ?? [])) {
        return true;
    }

    if (isset($permisosRol['funciones_por_par'][$par])) {
        return in_array($funcion, $permisosRol['funciones_por_par'][$par]);
    }

    return false;
}
