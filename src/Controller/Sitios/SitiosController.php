<?php

namespace BioGuppy\Controller\Sitios;

use BioGuppy\Model\Sitios\SitiosModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;
class SitiosController{

        private function consultarSeguro($obj, $sql, $params = []){
        try{
            return $obj->select($sql, $params);
        }catch(\Throwable $error){
            error_log("Consulta fallida en SitiosController: " . $error->getMessage());
            return false;
        }
    }

    use BitacoraTrait;

            public function listSit(){

        $obj = new SitiosModel();

        $sql = "SELECT s.codsitio AS id, s.nombresitio, c.nombrecomuna AS comuna,
                       b.nombrebarrio AS barrio, td.nombretipodeposito AS tipodeposito,
                       s.direccion, s.estado
                FROM tblsitio s
                INNER JOIN tblcomuna c ON c.codcomuna = s.codcomuna
                INNER JOIN tblbarrio b ON b.codbarrio = s.codbarrio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                ORDER BY s.nombresitio ASC";

        $resultado = $this->consultarSeguro($obj, $sql);
        $sitios = $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];

        include_once __DIR__ . '/../../../view/Sitios/listSit.php';

    }

   
    public function createSit(){

        $obj = new SitiosModel();

        $resComunas = $this->consultarSeguro($obj,
            "SELECT codcomuna AS id, nombrecomuna FROM tblcomuna WHERE estado = 'A' ORDER BY nombrecomuna ASC");
        $comunas = $resComunas ? $resComunas->fetchAll(PDO::FETCH_ASSOC) : [];

        $resBarrios = $this->consultarSeguro($obj,
            "SELECT codbarrio AS id, nombrebarrio, codcomuna FROM tblbarrio WHERE estado = 'A' ORDER BY nombrebarrio ASC");
        $barrios = $resBarrios ? $resBarrios->fetchAll(PDO::FETCH_ASSOC) : [];

        $resTipos = $this->consultarSeguro($obj,
            "SELECT codtipodeposito AS id, nombretipodeposito FROM tbltipodeposito WHERE estado = 'A' ORDER BY nombretipodeposito ASC");
        $tiposDeposito = $resTipos ? $resTipos->fetchAll(PDO::FETCH_ASSOC) : [];

        include_once __DIR__ . '/../../../view/Sitios/createSit.php';

    }

   
    public function postCreateSit(){

        $obj = new SitiosModel();

        $nombresitio    = $_POST['nombresitio'] ?? '';
        $codcomuna      = $_POST['codcomuna'] ?? '';
        $codbarrio      = $_POST['codbarrio'] ?? '';
        $codtipodeposito = $_POST['codtipodeposito'] ?? '';
        $direccion      = $_POST['direccion'] ?? '';

        if(empty(trim($nombresitio)) || empty($codcomuna) || empty($codbarrio) || empty($codtipodeposito) || empty(trim($direccion))){
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            redirect(getUrl('Sitios','Sitios','createSit'));
            exit();
        }

        //Validar formato
        if(mb_strlen($nombresitio) > 80){
            $_SESSION['error'] = "El nombre no puede tener más de 80 caracteres.";
            redirect(getUrl('Sitios','Sitios','createSit'));
            exit();
        }

        if(!preg_match('/^[\p{L} ]+$/u', $nombresitio)){
            $_SESSION['error'] = "El nombre solo puede contener letras y espacios (sin números, símbolos ni puntuación).";
            redirect(getUrl('Sitios','Sitios','createSit'));
            exit();
        }

        if(!preg_match('/^(Calle|Carrera|Avenida)\b/iu', $direccion)){
            $_SESSION['error'] = "La dirección debe iniciar con Calle, Carrera o Avenida.";
            redirect(getUrl('Sitios','Sitios','createSit'));
            exit();
        }

        $sqlValidar = "SELECT codsitio FROM tblsitio WHERE nombresitio ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombresitio])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe un sitio con ese nombre.";
            redirect(getUrl('Sitios','Sitios','createSit'));
            exit();
        }

        $nombreGuardado = trim($nombresitio);

        $sql = "INSERT INTO public.tblsitio (codsitio, codcomuna, codbarrio, codtipodeposito, nombresitio, direccion, fechacreacion, estado)
                VALUES (DEFAULT, :codcomuna, :codbarrio, :codtipodeposito, :nombresitio, :direccion, DEFAULT, DEFAULT)";

        $obj->insert($sql, [
            ':codcomuna'       => $codcomuna,
            ':codbarrio'       => $codbarrio,
            ':codtipodeposito' => $codtipodeposito,
            ':nombresitio'     => $nombreGuardado,
            ':direccion'       => trim($direccion),
        ]);

        // AUDITORÍA
        $nuevo = $obj->select("SELECT codsitio FROM tblsitio WHERE nombresitio = :nombre", [':nombre' => $nombreGuardado])
                      ->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'Sitios',
            $nuevo['codsitio'] ?? null,
            null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El sitio se registró exitosamente.";
        redirect(getUrl('Sitios','Sitios','listSit'));
        exit();

    }

    public function editSit(){

        $obj = new SitiosModel();
        $id = $_GET['id'] ?? null;

        $sql = "SELECT codsitio, codcomuna, codbarrio, codtipodeposito, nombresitio, direccion, estado
                FROM tblsitio WHERE codsitio = :id";
        $sitio = $obj->select($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        $resComunas = $this->consultarSeguro($obj,
            "SELECT codcomuna AS id, nombrecomuna FROM tblcomuna WHERE estado = 'A' ORDER BY nombrecomuna ASC");
        $comunas = $resComunas ? $resComunas->fetchAll(PDO::FETCH_ASSOC) : [];

        $resBarrios = $this->consultarSeguro($obj,
            "SELECT codbarrio AS id, nombrebarrio, codcomuna FROM tblbarrio WHERE estado = 'A' ORDER BY nombrebarrio ASC");
        $barrios = $resBarrios ? $resBarrios->fetchAll(PDO::FETCH_ASSOC) : [];

        $resTipos = $this->consultarSeguro($obj,
            "SELECT codtipodeposito AS id, nombretipodeposito FROM tbltipodeposito WHERE estado = 'A' ORDER BY nombretipodeposito ASC");
        $tiposDeposito = $resTipos ? $resTipos->fetchAll(PDO::FETCH_ASSOC) : [];

        include_once __DIR__ . '/../../../view/Sitios/editSit.php';

    }

    public function postUpdateSit(){

        $obj = new SitiosModel();

        $id              = $_POST['codsitio'] ?? null;
        $nombresitio     = $_POST['nombresitio'] ?? '';
        $codcomuna       = $_POST['codcomuna'] ?? '';
        $codbarrio       = $_POST['codbarrio'] ?? '';
        $codtipodeposito = $_POST['codtipodeposito'] ?? '';
        $direccion       = $_POST['direccion'] ?? '';

        if(empty($id)){
            $_SESSION['error'] = "Sitio no válido.";
            redirect(getUrl('Sitios','Sitios','listSit'));
            exit();
        }

        if(empty(trim($nombresitio)) || empty($codcomuna) || empty($codbarrio) || empty($codtipodeposito) || empty(trim($direccion))){
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            redirect(getUrl('Sitios','Sitios','editSit',['id'=>$id]));
            exit();
        }

        //Validar formato
        if(mb_strlen($nombresitio) > 80){
            $_SESSION['error'] = "El nombre no puede tener más de 80 caracteres.";
            redirect(getUrl('Sitios','Sitios','editSit',['id'=>$id]));
            exit();
        }

        if(!preg_match('/^[\p{L} ]+$/u', $nombresitio)){
            $_SESSION['error'] = "El nombre solo puede contener letras y espacios (sin números, símbolos ni puntuación).";
            redirect(getUrl('Sitios','Sitios','editSit',['id'=>$id]));
            exit();
        }

        if(!preg_match('/^(Calle|Carrera|Avenida)\b/iu', $direccion)){
            $_SESSION['error'] = "La dirección debe iniciar con Calle, Carrera o Avenida.";
            redirect(getUrl('Sitios','Sitios','editSit',['id'=>$id]));
            exit();
        }

        // valida que no exista OTRO sitio con ese nombre
        $sqlValidar = "SELECT codsitio FROM tblsitio WHERE nombresitio ILIKE :nombre AND codsitio != :id";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombresitio, ':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe otro sitio con ese nombre.";
            redirect(getUrl('Sitios','Sitios','editSit',['id'=>$id]));
            exit();
        }

        // AUDITORÍA
        $anterior = $obj->select("SELECT nombresitio FROM tblsitio WHERE codsitio = :id", [':id' => $id])
                         ->fetch(PDO::FETCH_ASSOC);

        $nombreGuardado = trim($nombresitio);

        $sql = "UPDATE tblsitio
                SET codcomuna = :codcomuna, codbarrio = :codbarrio, codtipodeposito = :codtipodeposito,
                    nombresitio = :nombresitio, direccion = :direccion
                WHERE codsitio = :id";

        $obj->update($sql, [
            ':codcomuna'       => $codcomuna,
            ':codbarrio'       => $codbarrio,
            ':codtipodeposito' => $codtipodeposito,
            ':nombresitio'     => $nombreGuardado,
            ':direccion'       => trim($direccion),
            ':id'              => $id,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Sitios',
            $id,
            $anterior['nombresitio'] ?? null,
            $nombreGuardado
        );

        $_SESSION['exito'] = "El sitio se actualizó correctamente.";
        redirect(getUrl('Sitios','Sitios','listSit'));
        exit();

    }

    public function delete(){

        $obj = new SitiosModel();
        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Sitio no válido.";
            redirect(getUrl('Sitios','Sitios','listSit'));
            exit();
        }

        $actual = $obj->select("SELECT estado FROM tblsitio WHERE codsitio = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
        $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

        $obj->update("UPDATE tblsitio SET estado = :estado WHERE codsitio = :id", [
            ':estado' => $nuevoEstado,
            ':id' => $id,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Sitios',
            $id,
            $actual['estado'] === 'A' ? 'Activo' : 'Inactivo',
            $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
        );

        $_SESSION['exito'] = "El estado del sitio se actualizó correctamente.";
        redirect(getUrl('Sitios','Sitios','listSit'));
        exit();

    }

    public function filtro(){

        $obj = new SitiosModel();

        $buscar = $_GET['buscar'] ?? '';

        $sql = "SELECT s.codsitio AS id, s.nombresitio, c.nombrecomuna AS comuna,
                       b.nombrebarrio AS barrio, td.nombretipodeposito AS tipodeposito,
                       s.direccion, s.estado
                FROM tblsitio s
                INNER JOIN tblcomuna c ON c.codcomuna = s.codcomuna
                INNER JOIN tblbarrio b ON b.codbarrio = s.codbarrio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                WHERE s.nombresitio ILIKE :buscar
                   OR s.direccion ILIKE :buscar
                   OR c.nombrecomuna ILIKE :buscar
                   OR b.nombrebarrio ILIKE :buscar
                   OR td.nombretipodeposito ILIKE :buscar
                ORDER BY s.nombresitio ASC";

        $resultado = $this->consultarSeguro($obj, $sql, [':buscar' => "%$buscar%"]);
        $sitios = $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];

        include_once __DIR__ . '/../../../view/Sitios/filtroSit.php';

    }

}