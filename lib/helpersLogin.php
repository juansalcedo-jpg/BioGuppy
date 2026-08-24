<?php

    if(!isset($_SESSION['auth']) || $_SESSION['auth'] != "ok"){
        redirect("inicio/login.php");
    }

?>