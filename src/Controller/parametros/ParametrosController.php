<?php

namespace BioGuppy\Controller\parametros;

use BioGuppy\Model\parametros\ParametrosModel;
use PDO;

class ParametrosController{

    private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en ParametrosController: " . $error->getMessage());
            return false;
        }
    }

    // ---------------------------------------------------------------
    // LISTADO PRINCIPAL (pestañas de Comunas y Barrios)
    // ---------------------------------------------------------------
    public function listParametros(){

        $obj = new ParametrosModel();

        $sqlComunas = "SELECT codcomuna AS id, nombrecomuna, estado
                        FROM tblcomuna
                        ORDER BY nombrecomuna ASC";
        $resultComunas = $this->consultarSeguro($obj, $sqlComunas);

        $sqlBarrios = "SELECT b.codbarrio AS id, b.nombrebarrio, b.estado,
                              c.codcomuna, c.nombrecomuna
                        FROM tblbarrio b
                        INNER JOIN tblcomuna c ON c.codcomuna = b.codcomuna
                        ORDER BY b.nombrebarrio ASC";
        $resultBarrios = $this->consultarSeguro($obj, $sqlBarrios);

        include_once __DIR__ . '/../../../view/parametrosSistema/listParametros.php';

    }

    // ---------------------------------------------------------------
    // COMUNAS
    // ---------------------------------------------------------------

    public function createComuna(){
        include_once __DIR__ . '/../../../view/parametrosSistema/createComuna.php';
    }

    public function postCreateComuna(){

        $obj = new ParametrosModel();

        $numero = trim($_POST['numero_comuna'] ?? '');

        if(empty($numero) || !ctype_digit($numero) || (int)$numero <= 0){
            $_SESSION['error'] = "Ingresa un número de comuna válido.";
            redirect(getUrl('Parametros','Parametros','createComuna'));
            exit();
        }

        $nombre = "Comuna " . (int)$numero;

        $sqlValidar = "SELECT codcomuna FROM tblcomuna WHERE nombrecomuna ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe esa comuna registrada.";
            redirect(getUrl('Parametros','Parametros','createComuna'));
            exit();
        }

        $sql = "INSERT INTO public.tblcomuna (codcomuna, nombrecomuna, estado)
                VALUES (DEFAULT, :nombre, DEFAULT)";

        $obj->insert($sql, [':nombre' => $nombre]);

        $_SESSION['exito'] = "La comuna se registró exitosamente.";
        redirect(getUrl('Parametros','Parametros','listParametros'));
        exit();

    }

    // inhabilitar / habilitar comuna
    public function deleteComuna(){

        $obj = new ParametrosModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Comuna no válida.";
            redirect(getUrl('Parametros','Parametros','listParametros'));
            exit();
        }

        $actual = $obj->select("SELECT estado FROM tblcomuna WHERE codcomuna = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblcomuna SET estado = :estado WHERE codcomuna = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $_SESSION['exito'] = "El estado de la comuna se actualizó correctamente.";
        redirect(getUrl('Parametros','Parametros','listParametros'));
        exit();

    }

    // ---------------------------------------------------------------
    // BARRIOS
    // ---------------------------------------------------------------

    public function createBarrio(){

        $obj = new ParametrosModel();

        $sqlComunas = "SELECT codcomuna, nombrecomuna FROM tblcomuna WHERE estado = 'A' ORDER BY nombrecomuna ASC";
        $resultComunas = $this->consultarSeguro($obj, $sqlComunas);

        include_once __DIR__ . '/../../../view/parametrosSistema/createBarrio.php';

    }

    public function postCreateBarrio(){

        $obj = new ParametrosModel();

        $codcomuna = $_POST['codcomuna'] ?? null;
        $nombre = $_POST['nombre_barrio'] ?? '';

        if(empty(trim($codcomuna)) || empty(trim($nombre))){
            $_SESSION['error'] = "La comuna y el nombre del barrio son obligatorios.";
            redirect(getUrl('Parametros','Parametros','createBarrio'));
            exit();
        }

        $sqlValidar = "SELECT codbarrio FROM tblbarrio WHERE nombrebarrio ILIKE :nombre AND codcomuna = :codcomuna";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombre, ':codcomuna' => $codcomuna])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe un barrio con ese nombre en esa comuna.";
            redirect(getUrl('Parametros','Parametros','createBarrio'));
            exit();
        }

        $sql = "INSERT INTO public.tblbarrio (codbarrio, codcomuna, nombrebarrio, estado)
                VALUES (DEFAULT, :codcomuna, :nombre, DEFAULT)";

        $obj->insert($sql, [
            ':codcomuna' => $codcomuna,
            ':nombre' => strtoupper(trim($nombre)),
        ]);

        $_SESSION['exito'] = "El barrio se registró exitosamente.";
        redirect(getUrl('Parametros','Parametros','listParametros'));
        exit();

    }

    // inhabilitar / habilitar barrio
    public function deleteBarrio(){

        $obj = new ParametrosModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Barrio no válido.";
            redirect(getUrl('Parametros','Parametros','listParametros'));
            exit();
        }

        $actual = $obj->select("SELECT estado FROM tblbarrio WHERE codbarrio = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblbarrio SET estado = :estado WHERE codbarrio = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $_SESSION['exito'] = "El estado del barrio se actualizó correctamente.";
        redirect(getUrl('Parametros','Parametros','listParametros'));
        exit();

    }

}