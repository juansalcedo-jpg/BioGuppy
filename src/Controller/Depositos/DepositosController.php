<?php

namespace BioGuppy\Controller\Depositos;

use BioGuppy\Model\Depositos\DepositosModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;
class DepositosController{

        private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en DepositosController: " . $error->getMessage());
            return false;
        }
    }

    use BitacoraTrait;

        //
    public function listDep(){

        $obj = new DepositosModel();

        $sql = "SELECT codtipodeposito AS id, nombretipodeposito, estado
                FROM tbltipodeposito
                ORDER BY nombretipodeposito ASC";

        $tiposDeposito = $this->consultarSeguro($obj, $sql);

        include_once __DIR__ . '/../../../view/Depositos/listDep.php';

    }

    // formulario de registro
    public function create(){
        include_once __DIR__ . '/../../../view/Depositos/createDep.php';
    }

    // valida y crea un nuevo tipo de deposito
    public function postCreateDep(){

        $obj = new DepositosModel();

        $nombre = $_POST['nombre_deposito'] ?? '';

        if(empty(trim($nombre))){
            $_SESSION['error'] = "El nombre del tipo de depósito es obligatorio.";
            redirect(getUrl('Depositos','Depositos','create'));
            exit();
        }

// valida que no exista ya un tipo de deposito con ese nombre
        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombretipodeposito ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe un tipo de depósito con ese nombre.";
            redirect(getUrl('Depositos','Depositos','create'));
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
            'Depositos',
            $nuevo['codtipodeposito'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de depósito se registró exitosamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    // formulario de edicion
    public function getUpdate(){

        $obj = new DepositosModel();

        $id = $_GET['id'];

        $sql = "SELECT codtipodeposito, nombretipodeposito AS nombredeposito, estado
                FROM tbltipodeposito WHERE codtipodeposito = :id";
        $tipoDeposito = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Depositos/getUpdateDep.php';

    }

    // guardar edicion
    public function postUpdateDep(){

        $obj = new DepositosModel();

        $id = $_POST['codtipodeposito'] ?? null;
        $nombre = $_POST['nombre_deposito'] ?? '';

        if(empty($id)){
            $_SESSION['error'] = "Tipo de depósito no válido.";
            redirect(getUrl('Depositos','Depositos','listDep'));
            exit();
        }

        if(empty(trim($nombre))){
            $_SESSION['error'] = "El nombre del tipo de depósito es obligatorio.";
            redirect(getUrl('Depositos','Depositos','getUpdate',['id'=>$id]));
            exit();
        }

    // valida que no exista otro tipo de deposito diferente a este con el mismo nombre

        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombretipodeposito ILIKE :nombre AND codtipodeposito != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe otro tipo de depósito con ese nombre.";
            redirect(getUrl('Depositos','Depositos','getUpdate',['id'=>$id]));
            exit();
        }

        // AUDITORÍA
        $anterior = $obj->select("SELECT nombretipodeposito FROM tbltipodeposito WHERE codtipodeposito = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = strtoupper(trim($nombre));

        $sql = "UPDATE tbltipodeposito SET nombretipodeposito = :nombre WHERE codtipodeposito = :id";

        $obj->update($sql, [':nombre' => $nombreGuardado, ':id' => $id]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Depositos',
            $id,
            $anterior['nombretipodeposito'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El tipo de depósito se actualizó correctamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    // inhabilitar y habilitar
    public function delete(){

        $obj = new DepositosModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Tipo de depósito no válido.";
            redirect(getUrl('Depositos','Depositos','listDep'));
            exit();
        }

        $actual = $obj->select("SELECT estado FROM tbltipodeposito WHERE codtipodeposito = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $estadoAnterior = $actual['estado'] ?? null;
        $nuevoEstado = ($estadoAnterior === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tbltipodeposito SET estado = :estado WHERE codtipodeposito = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Depositos',
            $id,
            $estadoAnterior === 'A' ? 'Activo' : 'Inactivo',
            $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
        );

        $_SESSION['exito'] = "El estado del tipo de depósito se actualizó correctamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    public function filtro(){

        $obj = new DepositosModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT codtipodeposito AS id, nombretipodeposito AS nombredeposito, estado
        FROM tbltipodeposito
        WHERE nombretipodeposito ILIKE :buscar
        ORDER BY nombretipodeposito ASC";

        $tiposDeposito = $this->consultarSeguro($obj, $sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Depositos/filtroDep.php';

    }

}