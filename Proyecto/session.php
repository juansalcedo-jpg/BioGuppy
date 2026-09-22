<?php
/**
 * session.php
 * Incluir este archivo al inicio de cada página protegida.
 * Si no hay sesión activa, redirige al login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
