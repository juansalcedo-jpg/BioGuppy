<?php

namespace BioGuppy\Controller\Catalogos;

require_once __DIR__ . '/../../../vendor/autoload.php';

use BioGuppy\Model\Catalogos\ActividadesTerreModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class ActividadesTerreController
{

    use BitacoraTrait;

    public function listActTerre(){

        $obj = new ActividadesTerreModel();

        $sql = "SELECT * FROM tbltipoactividadterreno ORDER BY codtipoactividad ASC";
        $actividades = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesTerre/ListActividadesTerre.php';

    }

    // formulario de registro
    public function createActTerre(){

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesTerre/CreateActividadesTerre.php';

    }

    // valida y crea un nuevo tipo de actividad de terreno
    public function postCreateActTerre(){

        $obj = new ActividadesTerreModel();

        $nombre = $_POST['nombreactividad'] ?? '';

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre de la actividad de terreno es obligatorio.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'createActTerre'));
            exit();
        }

        // valida que no exista ya una actividad de terreno con ese nombre
        $sqlValidar = "SELECT codtipoactividad FROM tbltipoactividadterreno WHERE nombreactividad ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe una actividad de terreno con ese nombre.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'createActTerre'));
            exit();
        }

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "INSERT INTO public.tbltipoactividadterreno (codtipoactividad, nombreactividad, estado)
                VALUES (DEFAULT, :nombre, DEFAULT)";

        $obj->insert($sql, [':nombre' => $nombreGuardado]);

        // AUDITORÍA
        $nuevo = $obj->select("SELECT codtipoactividad FROM tbltipoactividadterreno WHERE nombreactividad = :nombre", [':nombre' => $nombreGuardado])
                      ->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesTerre',
            $nuevo['codtipoactividad'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "La actividad de terreno se registró exitosamente.";
        redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
        exit();

    }

    // formulario de edicion
    public function getUpdateActTerre(){

        $obj = new ActividadesTerreModel();

        $id = $_GET['id'] ?? null;

        $sql = "SELECT * FROM tbltipoactividadterreno WHERE codtipoactividad = :id";
        $actividad = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesTerre/EditActividadesTerre.php';

    }

    // guardar edicion
    public function postUpdateActTerre(){

        $obj = new ActividadesTerreModel();

        $id = $_POST['codtipoactividad'] ?? null;
        $nombre = $_POST['nombreactividad'] ?? '';

        if (empty($id)) {
            $_SESSION['error'] = "Actividad de terreno no válida.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
            exit();
        }

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre de la actividad de terreno es obligatorio.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'getUpdateActTerre', ['id' => $id]));
            exit();
        }

        // valida que no exista otra actividad de terreno distinta a esta con el mismo nombre
        $sqlValidar = "SELECT codtipoactividad FROM tbltipoactividadterreno WHERE nombreactividad ILIKE :nombre AND codtipoactividad != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe otra actividad de terreno con ese nombre.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'getUpdateActTerre', ['id' => $id]));
            exit();
        }

        // AUDITORÍA
        $anterior = $obj->select("SELECT nombreactividad FROM tbltipoactividadterreno WHERE codtipoactividad = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "UPDATE tbltipoactividadterreno SET nombreactividad = :nombre WHERE codtipoactividad = :id";
        $obj->update($sql, [':nombre' => $nombreGuardado, ':id' => $id]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesTerre',
            $id,
            $anterior['nombreactividad'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "La actividad de terreno se actualizó correctamente.";
        redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
        exit();

    }

    // habilitar / inhabilitar
    public function activacion(){

        $obj = new ActividadesTerreModel();

        $id = $_GET['id'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "Actividad de terreno no válida.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
            exit();
        }

        // AUDITORÍA
        $actual = $obj->select("SELECT estado FROM tbltipoactividadterreno WHERE codtipoactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $estadoAnterior = $actual['estado'] ?? null;
        $nuevoEstado = ($estadoAnterior === 'A') ? 'I' : 'A';

        $execute = $obj->update("UPDATE tbltipoactividadterreno SET estado = :estado WHERE codtipoactividad = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        if ($execute) {

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'ActividadesTerre',
                $id,
                $estadoAnterior === 'A' ? 'Activo' : 'Inactivo',
                $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
            );

            $_SESSION['exito'] = "El estado de la actividad de terreno se actualizó correctamente.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
            exit();
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado de la actividad de terreno.";
            redirect(getUrl('Catalogos', 'ActividadesTerre', 'listActTerre'));
            exit();
        }

    }

    // buscador (ajax)
    public function filtro(){

        $obj = new ActividadesTerreModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT * FROM tbltipoactividadterreno
                WHERE nombreactividad ILIKE :buscar
                ORDER BY nombreactividad ASC";

        $actividades = $obj->select($sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesTerre/filtroActividadesTerre.php';

    }

}

?>