<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\AlimentacionModel;
use PDO;

class AlimentacionController{

    public function Alimentacion(){
        include_once __DIR__ . '/../../../view/ActividadesZoo/Alimentacion.php';
    }
}

?>