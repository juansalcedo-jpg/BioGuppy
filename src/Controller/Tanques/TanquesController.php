<?php

    namespace BioGuppy\Controller\Tanques;

    use BioGuppy\Model\Tanques\TanquesModel;
    use PDO;

    class TanquesController{

        public function listTan(){
            include_once __DIR__ . '/../../../view/Tanques/listTan.php';
        }

    }

?>