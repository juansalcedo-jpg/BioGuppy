<?php

    namespace BioGuppy\Controller\ReportesZoo;

    use BioGuppy\Model\ReportesZoo\ReportesZooModel;
    use PDO;

    class ReportesZooController{

        public function listRepoZoo(){
            include_once __DIR__ . '/../../../view/ReportesZoo/listRepoZoo.php';
        }

    }

?>