<?php

include_once '../lib/helpers.php';
include_once '../lib/helpersLogin.php';
include_once '../view/partials/head.php';

echo "<body>";
echo "<div class='app-layout'>";

    include_once '../view/partials/navbarLateral.php';

    echo "<div class='main-content'>";
        include_once '../view/partials/navbarSuperior.php';
        echo "<div class='page-content'>";
        if(isset($_GET['modulo'])){
            resolve();
        }else{
            echo "hola";
        }
        echo "</div>";
    echo "</div>";

echo "</div>";
include_once '../view/partials/footer.php';
echo "</body>";
echo "</html>";

?>