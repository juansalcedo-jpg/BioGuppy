<?php

$GLOBALS['PERMISOS_POR_ROL'] = [

    'Super Admin' => [
    'pares' => ['Usuarios:Usuarios', 'Roles:Roles', 'Auditoria:Auditoria', 'Parametros:Parametros', 'SobreNosotros:SobreNosotros'],
],

'Administrador' => [
    'pares' => ['Dashboard:Dashboard', 'Catalogos:Catalogos', 'Catalogos:TipoTanque', 'Catalogos:TipoDeposito', 'Catalogos:ActividadesZoo', 'Catalogos:ActividadesTerre', 'SobreNosotros:SobreNosotros'],
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
        'SobreNosotros:SobreNosotros',
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
        'ActividadesZoo:Limpieza',
        'ActividadesZoo:AjusteNivel',
        'ActividadesZoo:Lavado',
        'SobreNosotros:SobreNosotros',
    ],
],

'Auxiliar Terreno' => [
    'pares' => ['SobreNosotros:SobreNosotros'],
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
