<?php

include_once '../lib/helpers.php';
include_once '../lib/helpersLogin.php';
include_once '../view/partials/head.php';

echo "<body>";
echo "<div class='container'>";
include_once '../view/partials/navbarLateral.php';
if(isset($_GET['modulo'])){
    resolve();
}else{
}
echo "</div>";
include_once '../view/partials/footer.php';
echo "</body>";
echo "</html>";

?>