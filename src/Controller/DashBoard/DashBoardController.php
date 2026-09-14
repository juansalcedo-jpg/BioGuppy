<?php

namespace BioGuppy\Controller\DashBoard;

use BioGuppy\Model\DashBoard\DashBoardModel;
use PDO;

class DashBoardController{

    public function listDashboard(){
        include_once __DIR__ . '/../../../view/DashBoard/DashBoard.php';
    }
}

?>