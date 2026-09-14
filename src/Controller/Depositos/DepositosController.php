<?php

namespace BioGuppy\Controller\Depositos;

use BioGuppy\Model\Depositos\DepositosModel;
use PDO;

// Este controlador maneja el CATALOGO de tipos de deposito
// (tbltipodeposito): Piscina abandonada, aguas estancadas, fuente,
// construccion. El "Sitio" (lugar fisico) lo maneja el Coordinador en
// su propio modulo; el Auxiliar Terreno solo consulta/mantiene el
// catalogo de tipos que despues selecciona al registrar un Sitio o una
// actividad.
class DepositosController{

    // ---------------------------------------------------------------
    // HELPER: ejecuta una consulta de SOLO LECTURA (para mostrar
    // datos) protegida contra errores de base de datos -- si la
    // tabla no existe, la columna cambio, o la conexion falla, en
    // vez de dejar que el error rompa toda la pagina, se devuelve
    // "false" y la VISTA se encarga de mostrar su estado normal de
    // "no hay datos" (exactamente lo mismo que ya pasa cuando la
    // tabla existe pero esta vacia). El detalle tecnico igual queda
    // en el log del servidor para poder depurarlo.
    //
    // OJO: esto es SOLO para consultas que alimentan una vista de
    // lectura (listar, buscar, llenar un combo). Las consultas que
    // son parte de GUARDAR datos (insert/update) no usan este
    // helper a proposito: si un guardado falla, el usuario SI debe
    // enterarse (no se le puede hacer creer que guardo cuando no).
    // ---------------------------------------------------------------
    private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en DepositosController: " . $error->getMessage());
            return false;
        }
    }

    // ---------------------------------------------------------------
    // LISTAR: todos los tipos de deposito del catalogo.
    // ---------------------------------------------------------------
    public function listDep(){

        $obj = new DepositosModel();

        $sql = "SELECT codtipodeposito AS id, nombredeposito, fechacreacion, estado
                FROM tbltipodeposito
                ORDER BY nombredeposito ASC";

        $tiposDeposito = $this->consultarSeguro($obj, $sql);

        include_once __DIR__ . '/../../../view/Depositos/listDep.php';

    }

    // ---------------------------------------------------------------
    // FORMULARIO DE REGISTRO
    // ---------------------------------------------------------------
    public function create(){
        include_once __DIR__ . '/../../../view/Depositos/createDep.php';
    }

    // ---------------------------------------------------------------
    // GUARDAR REGISTRO: valida y crea un nuevo tipo de deposito.
    // ---------------------------------------------------------------
    public function postCreateDep(){

        $obj = new DepositosModel();

        $nombre = $_POST['nombre_deposito'] ?? '';

        if(empty(trim($nombre))){
            $_SESSION['error'] = "El nombre del tipo de depósito es obligatorio.";
            redirect(getUrl('Depositos','Depositos','create'));
            exit();
        }

        // Validando que no exista ya un tipo de deposito con ese nombre
        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombredeposito ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe un tipo de depósito con ese nombre.";
            redirect(getUrl('Depositos','Depositos','create'));
            exit();
        }

        $sql = "INSERT INTO public.tbltipodeposito (codtipodeposito, nombredeposito, fechacreacion, estado)
                VALUES (DEFAULT, :nombre, DEFAULT, DEFAULT)";

        $obj->insert($sql, [':nombre' => strtoupper(trim($nombre))]);

        $_SESSION['exito'] = "El tipo de depósito se registró exitosamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    // ---------------------------------------------------------------
    // FORMULARIO DE EDICION
    // ---------------------------------------------------------------
    public function getUpdate(){

        $obj = new DepositosModel();

        $id = $_GET['id'];

        $sql = "SELECT * FROM tbltipodeposito WHERE codtipodeposito = :id";
        $tipoDeposito = $obj->select($sql, [':id' => $id]);

        include_once __DIR__ . '/../../../view/Depositos/getUpdateDep.php';

    }

    // ---------------------------------------------------------------
    // GUARDAR EDICION
    // ---------------------------------------------------------------
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

        // Validando que no exista OTRO tipo de deposito (distinto a este) con el mismo nombre
        $sqlValidar = "SELECT codtipodeposito FROM tbltipodeposito WHERE nombredeposito ILIKE :nombre AND codtipodeposito != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe otro tipo de depósito con ese nombre.";
            redirect(getUrl('Depositos','Depositos','getUpdate',['id'=>$id]));
            exit();
        }

        $sql = "UPDATE tbltipodeposito SET nombredeposito = :nombre WHERE codtipodeposito = :id";
        $obj->update($sql, [':nombre' => strtoupper(trim($nombre)), ':id' => $id]);

        $_SESSION['exito'] = "El tipo de depósito se actualizó correctamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    // ---------------------------------------------------------------
    // INHABILITAR / HABILITAR
    // ---------------------------------------------------------------
    public function delete(){

        $obj = new DepositosModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Tipo de depósito no válido.";
            redirect(getUrl('Depositos','Depositos','listDep'));
            exit();
        }

        $actual = $obj->select("SELECT estado FROM tbltipodeposito WHERE codtipodeposito = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tbltipodeposito SET estado = :estado WHERE codtipodeposito = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $_SESSION['exito'] = "El estado del tipo de depósito se actualizó correctamente.";
        redirect(getUrl('Depositos','Depositos','listDep'));
        exit();

    }

    // ---------------------------------------------------------------
    // BUSCADOR (ajax)
    // ---------------------------------------------------------------
    public function filtro(){

        $obj = new DepositosModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT codtipodeposito AS id, nombredeposito, fechacreacion, estado
                FROM tbltipodeposito
                WHERE nombredeposito ILIKE :buscar
                ORDER BY nombredeposito ASC";

        $tiposDeposito = $this->consultarSeguro($obj, $sql, [':buscar' => "%$buscar%"]);

        include_once __DIR__ . '/../../../view/Depositos/filtroDep.php';

    }

}
