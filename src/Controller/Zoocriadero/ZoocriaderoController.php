<?php

    namespace BioGuppy\Controller\Zoocriadero;

    use BioGuppy\Model\Usuarios\UsuariosModel;
    use PDO;

    class ZoocriaderoController{

        public function listHistZoo(){
            include_once __DIR__ . '/../../../view/Zoocriadero/listZoo.php';
        }

    }

?>