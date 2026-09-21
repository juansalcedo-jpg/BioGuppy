<?php

namespace BioGuppy\Controller\SobreNosotros;

use BioGuppy\Model\SobreNosotros\SobreNosotrosModel;
use PDO;
class SobreNosotrosController{

    public function index(){

        $obj = new SobreNosotrosModel();

        $zoocriaderosActivos = $obj->select(
            "SELECT COUNT(*) AS total FROM tblzoocriadero WHERE estado = 'A'"
        )->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $sitiosActivos = $obj->select(
            "SELECT COUNT(*) AS total FROM tblsitio WHERE estado = 'A'"
        )->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        include_once __DIR__ . '/../../../view/SobreNosotros/index.php';

    }

}