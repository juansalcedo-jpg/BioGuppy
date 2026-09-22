<?php
include_once '../lib/helpers.php';
include_once '../lib/helpersLogin.php';
include_once '../view/partials/head.php';

echo "<body>";
echo "<div class='app-layout'>";
echo "<div class='sidebar-backdrop'></div>"; 

    if(!isset($_GET['modulo'])){
        $_GET['modulo']      = $_SESSION['modulo']      ?? null;
        $_GET['controlador'] = $_SESSION['controlador'] ?? null;
        $_GET['funcion']     = $_SESSION['funcion']     ?? null;
    }

    include_once '../view/partials/navbarLateral.php';

    echo "<div class='main-content'>";
        include_once '../view/partials/navbarSuperior.php';
        echo "<div class='page-content'>";
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