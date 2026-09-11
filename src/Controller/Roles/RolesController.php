<?php

namespace BioGuppy\Controller\Roles;

use BioGuppy\Model\Roles\RolesModel;
use PDO;

class RolesController{

    public function createRol(){

        include_once __DIR__ . '/../../../view/Roles/createRol.php';

    }

    public function listRol(){

        include_once __DIR__ . '/../../../view/Roles/listRol.php';

    }

}