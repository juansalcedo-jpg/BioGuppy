<?php

namespace BioGuppy\Controller\Manuales;

/*
 * Módulo "Manuales": lo ven todos los roles (está en MODULOS_COMUNES de
 * lib/permisos.php), pero cada rol ve su propio video.
 * Los videos van en la carpeta img/ de la raíz del proyecto.
 */
class ManualesController{

    public function video(){
        $rol = $_SESSION['nombre_rol'] ?? '';

        $videos = [
            'Super Admin'                   => ['archivo' => 'superadmin.mp4',     'titulo' => 'Manual del Super Administrador'],
            'Administrador'                 => ['archivo' => 'administrador.mp4',  'titulo' => 'Manual del Administrador'],
            'Coordinador Control Biologico' => ['archivo' => 'coordinador.mp4',    'titulo' => 'Manual del Coordinador de Control Biológico'],
            'Auxiliar Terreno'              => ['archivo' => 'auxterreno.mp4',     'titulo' => 'Manual del Auxiliar de Terreno'],
            'Auxiliar Zoocriadero'          => ['archivo' => 'auxzoocriadero.mp4', 'titulo' => 'Manual del Auxiliar de Zoocriadero'],
        ];

        $manual = $videos[$rol] ?? null;

        $videoUrl = null;

        if($manual){
            $rutaFisica = __DIR__ . '/../../../img/' . $manual['archivo'];

            if(file_exists($rutaFisica)){
                $videoUrl = '../img/' . $manual['archivo'];
            }
        }

        include_once __DIR__ . '/../../../view/Manuales/video.php';
    }
}
