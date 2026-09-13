<?php

namespace BioGuppy\Controller\ActividadesZoo;

use BioGuppy\Model\ActividadesZoo\NacidosMuertosModel;
use PDO;

class NacidosMuertosController{

    public function NacidosMuertos(){
        include_once __DIR__ . '/../../../view/ActividadesZoo/NacidosMuertos.php';
    }
}
