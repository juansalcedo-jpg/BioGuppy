<?php

namespace BioGuppy\Controller\Catalogos;

require_once __DIR__ . '/../../../vendor/autoload.php';

use BioGuppy\Model\Catalogos\TipoTanqueModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class TipoTanqueController
{

    use BitacoraTrait;

    public function listTipoTanq(){

        $obj = new TipoTanqueModel();

        $sql = "SELECT * FROM tbltipotanque ORDER BY codtipotanque ASC";
        $tanques = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Catalogos/TipoTanque/ListTipoTanque.php';

    }

    // formulario de registro
    public function createTipoTanque(){

        include_once __DIR__ . '/../../../view/Catalogos/TipoTanque/CreateTipoTanque.php';

    }

    // valida y crea un nuevo tipo de tanque
    public function postCreateTipoTanque(){

        $obj = new TipoTanqueModel();

        $nombre = $_POST['nombretanque'] ?? '';

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre del tipo de tanque es obligatorio.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'createTipoTanque'));
            exit();
        }

        // valida que no exista ya un tipo de tanque con ese nombre
        $sqlValidar = "SELECT codtipotanque FROM tbltipotanque WHERE nombretipotanque ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe un tipo de tanque con ese nombre.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'createTipoTanque'));
            exit();
        }

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "INSERT INTO public.tbltipotanque (codtipotanque, nombretipotanque, estado)
                VALUES (DEFAULT, :nombre, DEFAULT)";

        $obj->insert($sql, [':nombre' => $nombreGuardado]);

        // AUDITORÍA: buscamos el id recién creado (el nombre es único gracias a la validación de arriba)
        $nuevo = $obj->select("SELECT codtipotanque FROM tbltipotanque WHERE nombretipotanque = :nombre", [':nombre' => $nombreGuardado])
                      ->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'TipoTanque',
            $nuevo['codtipotanque'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de tanque se registró exitosamente.";
        redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
        exit();

    }

    // formulario de edicion
    public function getUpdateTipoTanque(){

        $obj = new TipoTanqueModel();

        $id = $_GET['id'] ?? null;

        $sql = "SELECT * FROM tbltipotanque WHERE codtipotanque = :id";
        $tipoTanque = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Catalogos/TipoTanque/EditTipoTanque.php';

    }

    // guardar edicion
    public function postUpdateTipoTanque(){

        $obj = new TipoTanqueModel();

        $id = $_POST['codtipotanque'] ?? null;
        $nombre = $_POST['nombretanque'] ?? '';

        if (empty($id)) {
            $_SESSION['error'] = "Tipo de tanque no válido.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
            exit();
        }

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre del tipo de tanque es obligatorio.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'getUpdateTipoTanque', ['id' => $id]));
            exit();
        }

        // valida que no exista otro tipo de tanque distinto a este con el mismo nombre
        $sqlValidar = "SELECT codtipotanque FROM tbltipotanque WHERE nombretipotanque ILIKE :nombre AND codtipotanque != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe otro tipo de tanque con ese nombre.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'getUpdateTipoTanque', ['id' => $id]));
            exit();
        }

        // AUDITORÍA: capturamos el valor anterior antes de sobreescribirlo
        $anterior = $obj->select("SELECT nombretipotanque FROM tbltipotanque WHERE codtipotanque = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "UPDATE tbltipotanque SET nombretipotanque = :nombre WHERE codtipotanque = :id";
        $obj->update($sql, [':nombre' => $nombreGuardado, ':id' => $id]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'TipoTanque',
            $id,
            $anterior['nombretipotanque'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de tanque se actualizó correctamente.";
        redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
        exit();

    }

    // habilitar / inhabilitar
    public function activacion(){

        $obj = new TipoTanqueModel();

        $id = $_GET['id'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "Tipo de tanque no válido.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
            exit();
        }

        // AUDITORÍA: se consulta el estado real en BD (no se confía en lo que llegue por la URL)
        $actual = $obj->select("SELECT estado FROM tbltipotanque WHERE codtipotanque = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $estadoAnterior = $actual['estado'] ?? null;
        $nuevoEstado = ($estadoAnterior === 'A') ? 'I' : 'A';

        $execute = $obj->update("UPDATE tbltipotanque SET estado = :estado WHERE codtipotanque = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        if ($execute) {

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'TipoTanque',
                $id,
                $estadoAnterior === 'A' ? 'Activo' : 'Inactivo',
                $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
            );

            $_SESSION['exito'] = "El estado del tipo de tanque se actualizó correctamente.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
            exit();
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado del tipo de tanque.";
            redirect(getUrl('Catalogos', 'TipoTanque', 'listTipoTanq'));
            exit();
        }

    }

    // buscador (ajax)
    public function filtro(){

        $obj = new TipoTanqueModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT * FROM tbltipotanque
                WHERE nombretipotanque ILIKE :buscar
                ORDER BY nombretipotanque ASC";

        $tanques = $obj->select($sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Catalogos/TipoTanque/filtroTipoTanque.php';

    }

}

?>
