<?php
include_once '../lib/helpers.php';
include_once '../lib/helpersLogin.php';

if(!isset($_GET['modulo'])){
    $_GET['modulo']      = $_SESSION['modulo']      ?? null;
    $_GET['controlador'] = $_SESSION['controlador'] ?? null;
    $_GET['funcion']     = $_SESSION['funcion']     ?? null;
}

// Se valida el permiso ANTES de imprimir la página, para que alguien que
// escriba la URL de un módulo que no tiene no alcance a ver nada.
if(isset($_GET['modulo'])){
    verificarAccesoRuta($_GET['modulo']);
}

include_once '../view/partials/head.php';

echo "<body>";
echo "<div class='app-layout'>";
echo "<div class='sidebar-backdrop'></div>"; 

    include_once '../view/partials/navbarLateral.php';

    echo "<div class='main-content'>";
        include_once '../view/partials/navbarSuperior.php';
        echo "<div class='page-content'>";
        if(isset($_SESSION['acceso_denegado'])){
            echo "<div class='alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 m-3 mb-0' role='alert'>";
            echo "<i class='bi bi-shield-exclamation fs-5'></i><div>" . htmlspecialchars($_SESSION['acceso_denegado']) . "</div>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Cerrar'></button></div>";
            unset($_SESSION['acceso_denegado']);
        }
        if(isset($_GET['modulo'])){
            resolve();
        }
        echo "</div>";
    echo "</div>";

echo "</div>";
include_once '../view/partials/footer.php';
echo "</body>";
echo "</html>";
?>