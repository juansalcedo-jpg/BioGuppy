<?php

namespace BioGuppy\Controller\Catalogos;

require_once __DIR__ . '/../../../vendor/autoload.php';

use BioGuppy\Model\Catalogos\ActividadesZooModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class ActividadesZooController
{

    use BitacoraTrait;

    public function listActZoo(){

        $obj = new ActividadesZooModel();

        $sql = "SELECT * FROM tbltipoactividadzoo ORDER BY codtipoactividad ASC";
        $actividades = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesZoo/ListActividadesZoo.php';

    }

    // formulario de registro
    public function createActZoo(){

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesZoo/CreateActividadesZoo.php';

    }

    // valida y crea un nuevo tipo de actividad de zoocriadero
    public function postCreateActZoo(){

        $obj = new ActividadesZooModel();

        $nombre = $_POST['nombreactividad'] ?? '';

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre de la actividad de zoocriadero es obligatorio.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'createActZoo'));
            exit();
        }

        // valida que no exista ya una actividad de zoocriadero con ese nombre
        $sqlValidar = "SELECT codtipoactividad FROM tbltipoactividadzoo WHERE nombreactividad ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe una actividad de zoocriadero con ese nombre.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'createActZoo'));
            exit();
        }

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "INSERT INTO public.tbltipoactividadzoo (codtipoactividad, nombreactividad, estado)
                VALUES (DEFAULT, :nombre, DEFAULT)";

        $obj->insert($sql, [':nombre' => $nombreGuardado]);

        // AUDITORÍA
        $nuevo = $obj->select("SELECT codtipoactividad FROM tbltipoactividadzoo WHERE nombreactividad = :nombre", [':nombre' => $nombreGuardado])
                      ->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'ActividadesZoo',
            $nuevo['codtipoactividad'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "La actividad de zoocriadero se registró exitosamente.";
        redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
        exit();

    }

    // formulario de edicion
    public function getUpdateActZoo(){

        $obj = new ActividadesZooModel();

        $id = $_GET['id'] ?? null;

        $sql = "SELECT * FROM tbltipoactividadzoo WHERE codtipoactividad = :id";
        $actividad = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesZoo/EditActividadesZoo.php';

    }

    // guardar edicion
    public function postUpdateActZoo(){

        $obj = new ActividadesZooModel();

        $id = $_POST['codtipoactividad'] ?? null;
        $nombre = $_POST['nombreactividad'] ?? '';

        if (empty($id)) {
            $_SESSION['error'] = "Actividad de zoocriadero no válida.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
            exit();
        }

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre de la actividad de zoocriadero es obligatorio.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'getUpdateActZoo', ['id' => $id]));
            exit();
        }

        // valida que no exista otra actividad de zoocriadero distinta a esta con el mismo nombre
        $sqlValidar = "SELECT codtipoactividad FROM tbltipoactividadzoo WHERE nombreactividad ILIKE :nombre AND codtipoactividad != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe otra actividad de zoocriadero con ese nombre.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'getUpdateActZoo', ['id' => $id]));
            exit();
        }

        // AUDITORÍA
        $anterior = $obj->select("SELECT nombreactividad FROM tbltipoactividadzoo WHERE codtipoactividad = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "UPDATE tbltipoactividadzoo SET nombreactividad = :nombre WHERE codtipoactividad = :id";
        $obj->update($sql, [':nombre' => $nombreGuardado, ':id' => $id]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'ActividadesZoo',
            $id,
            $anterior['nombreactividad'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "La actividad de zoocriadero se actualizó correctamente.";
        redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
        exit();

    }

    // habilitar / inhabilitar
    public function activacion(){

        $obj = new ActividadesZooModel();

        $id = $_GET['id'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "Actividad de zoocriadero no válida.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
            exit();
        }

        // AUDITORÍA
        $actual = $obj->select("SELECT estado FROM tbltipoactividadzoo WHERE codtipoactividad = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $estadoAnterior = $actual['estado'] ?? null;
        $nuevoEstado = ($estadoAnterior === 'A') ? 'I' : 'A';

        $execute = $obj->update("UPDATE tbltipoactividadzoo SET estado = :estado WHERE codtipoactividad = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        if ($execute) {

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'ActividadesZoo',
                $id,
                $estadoAnterior === 'A' ? 'Activo' : 'Inactivo',
                $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
            );

            $_SESSION['exito'] = "El estado de la actividad de zoocriadero se actualizó correctamente.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
            exit();
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado de la actividad de zoocriadero.";
            redirect(getUrl('Catalogos', 'ActividadesZoo', 'listActZoo'));
            exit();
        }

    }

    // buscador (ajax)
    public function filtro(){

        $obj = new ActividadesZooModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT * FROM tbltipoactividadzoo
                WHERE nombreactividad ILIKE :buscar
                ORDER BY nombreactividad ASC";

        $actividades = $obj->select($sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Catalogos/ActividadesZoo/filtroActividadesZoo.php';

    }

}

?>
