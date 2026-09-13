<?php

    namespace BioGuppy\Controller\HistorialZoo;

    use BioGuppy\Model\HistorialZoo\HistorialZooModel;
    use PDO;

    class HistorialZooController{

        public function listHistZoo(){
            include_once __DIR__ . '/../../../view/HistorialZoo/listHistZoo.php';
        }

    }

?>