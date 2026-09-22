<?php
// Este archivo se mantiene solo por compatibilidad con enlaces de
// recuperación enviados antes de este cambio. El flujo actual vive
// completamente en login.php: ahí el propio JavaScript valida el
// token por AJAX y abre el modal de nueva contraseña.
session_start();

$token = trim($_GET['token'] ?? '');
$destino = 'login.php' . ($token !== '' ? ('?token=' . urlencode($token)) : '');

header('Location: ' . $destino);
exit;
