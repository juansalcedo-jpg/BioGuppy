<?php

namespace BioGuppy\Controller\Catalogos;

require_once __DIR__ . '/../../../vendor/autoload.php';

use BioGuppy\Model\Catalogos\TipoDepositoModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class TipoDepositoController
{

    use BitacoraTrait;

    public function listTipoDepo(){

        $obj = new TipoDepositoModel();

        $sql = "SELECT * FROM tbltipodeposito ORDER BY codtipodeposito ASC";
        $depositos = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Catalogos/TipoDeposito/ListTipoDeposito.php';

    }

    // formulario de registro
    public function createTipoDeposito(){

        include_once __DIR__ . '/../../../view/Catalogos/TipoDeposito/CreateTipoDeposito.php';

    }

    // valida y crea un nuevo tipo de deposito
    public function postCreateTipoDeposito(){

        $obj = new TipoDepositoModel();

        $nombre = $_POST['nombretipodeposito'] ?? '';

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre del tipo de depósito es obligatorio.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'createTipoDeposito'));
            exit();
        }

        // valida que no exista ya un tipo de deposito con ese nombre
        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombretipodeposito ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe un tipo de depósito con ese nombre.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'createTipoDeposito'));
            exit();
        }

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "INSERT INTO public.tbltipodeposito (codtipodeposito, nombretipodeposito, estado)
                VALUES (DEFAULT, :nombre, DEFAULT)";

        $obj->insert($sql, [':nombre' => $nombreGuardado]);

        // AUDITORÍA: buscamos el id recién creado (el nombre es único gracias a la validación de arriba)
        $nuevo = $obj->select("SELECT codtipodeposito FROM tbltipodeposito WHERE nombretipodeposito = :nombre", [':nombre' => $nombreGuardado])
                      ->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'TipoDeposito',
            $nuevo['codtipodeposito'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de depósito se registró exitosamente.";
        redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
        exit();

    }

    // formulario de edicion
    public function getUpdateTipoDeposito(){

        $obj = new TipoDepositoModel();

        $id = $_GET['id'] ?? null;

        $sql = "SELECT * FROM tbltipodeposito WHERE codtipodeposito = :id";
        $tipoDeposito = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Catalogos/TipoDeposito/EditTipoDeposito.php';

    }

    // guardar edicion
    public function postUpdateTipoDeposito(){

        $obj = new TipoDepositoModel();

        $id = $_POST['codtipodeposito'] ?? null;
        $nombre = $_POST['nombretipodeposito'] ?? '';

        if (empty($id)) {
            $_SESSION['error'] = "Tipo de depósito no válido.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
            exit();
        }

        if (empty(trim($nombre))) {
            $_SESSION['error'] = "El nombre del tipo de depósito es obligatorio.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'getUpdateTipoDeposito', ['id' => $id]));
            exit();
        }

        // valida que no exista otro tipo de deposito distinto a este con el mismo nombre
        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombretipodeposito ILIKE :nombre AND codtipodeposito != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['error'] = "Ya existe otro tipo de depósito con ese nombre.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'getUpdateTipoDeposito', ['id' => $id]));
            exit();
        }

        // AUDITORÍA: capturamos el valor anterior antes de sobreescribirlo
        $anterior = $obj->select("SELECT nombretipodeposito FROM tbltipodeposito WHERE codtipodeposito = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "UPDATE tbltipodeposito SET nombretipodeposito = :nombre WHERE codtipodeposito = :id";
        $obj->update($sql, [':nombre' => $nombreGuardado, ':id' => $id]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'TipoDeposito',
            $id,
            $anterior['nombretipodeposito'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de depósito se actualizó correctamente.";
        redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
        exit();

    }

    // habilitar / inhabilitar
    public function activacion(){

        $obj = new TipoDepositoModel();

        $id = $_GET['id'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "Tipo de depósito no válido.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
            exit();
        }

        // AUDITORÍA: se consulta el estado real en BD (no se confía en lo que llegue por la URL)
        $actual = $obj->select("SELECT estado FROM tbltipodeposito WHERE codtipodeposito = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $estadoAnterior = $actual['estado'] ?? null;
        $nuevoEstado = ($estadoAnterior === 'A') ? 'I' : 'A';

        $execute = $obj->update("UPDATE tbltipodeposito SET estado = :estado WHERE codtipodeposito = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        if ($execute) {

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'TipoDeposito',
                $id,
                $estadoAnterior === 'A' ? 'Activo' : 'Inactivo',
                $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
            );

            $_SESSION['exito'] = "El estado del tipo de depósito se actualizó correctamente.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
            exit();
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado del tipo de depósito.";
            redirect(getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo'));
            exit();
        }

    }

    // buscador (ajax)
    public function filtro(){

        $obj = new TipoDepositoModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT * FROM tbltipodeposito
                WHERE nombretipodeposito ILIKE :buscar
                ORDER BY nombretipodeposito ASC";

        $depositos = $obj->select($sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Catalogos/TipoDeposito/filtroTipoDeposito.php';

    }

}

?>
