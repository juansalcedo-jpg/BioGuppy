<?php

    if(!isset($_SESSION['auth']) || $_SESSION['auth'] != "ok"){
        irA("inicio/login.php");
        exit();
    }

?>