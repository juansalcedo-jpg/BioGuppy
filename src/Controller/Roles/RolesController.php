<?php

namespace BioGuppy\Controller\Roles;

use BioGuppy\Model\Roles\rolesModel;
use PDO;
use BioGuppy\Controller\Traits\BitacoraTrait;

class RolesController{

    use BitacoraTrait;

    public function createRol(){
        include_once __DIR__ . '/../../../view/Roles/createRol.php';
    }

    public function postcreateRol(){

        $obj = new rolesModel();

        $nombreRol = $_POST['nombreRol'] ?? '';
        $descripcionRol = $_POST['descripcionRol'] ?? '';

        if(empty(trim($nombreRol))){
            $_SESSION['error'] = "El nombre del rol es obligatorio.";
            redirect(getUrl('Roles','Roles','createRol'));
            exit();
        }

        $sqlValidar = "SELECT codrol FROM tblrol WHERE nombrerol ILIKE :nombre";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombreRol])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe un rol con ese nombre.";
            redirect(getUrl('Roles','Roles','createRol'));
            exit();
        }

        $sql = "INSERT INTO tblrol (nombrerol)
                VALUES (:nombre)
                RETURNING codrol";

        $stmt = $obj->insert($sql, [
            ':nombre' => $nombreRol
        ]);

        $nuevo = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'Roles',
            $nuevo['codrol'] ?? null,
            null,
            "Rol: $nombreRol" . (!empty($descripcionRol) ? " — $descripcionRol" : "")
        );

        $_SESSION['exito'] = "El rol se registró correctamente.";
        redirect(getUrl('Roles','Roles','listRol'));
        exit();

    }

    public function listRol(){

        $obj = new rolesModel();

        $sql = "SELECT * FROM tblrol ORDER BY codrol ASC";

        $resultrol = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Roles/listRol.php';

    }

    public function editRol(){

        $obj = new rolesModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Rol no válido.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        $rol = $obj->select("SELECT codrol, nombrerol FROM tblrol WHERE codrol = :id", [':id' => $id]);

        include_once __DIR__ . '/../../../view/Roles/editRol.php';

    }

    public function postUpdateRol(){

        $obj = new rolesModel();

        $codrol = $_POST['codrol'] ?? null;
        $nombreRol = $_POST['nombreRol'] ?? '';

        if(empty($codrol) || empty(trim($nombreRol))){
            $_SESSION['error'] = "El nombre del rol es obligatorio.";
            redirect(getUrl('Roles','Roles','editRol', ['id' => $codrol]));
            exit();
        }

        $sqlValidar = "SELECT codrol FROM tblrol WHERE nombrerol ILIKE :nombre AND codrol != :codrol";
        $existe = $obj->select($sqlValidar, [':nombre' => $nombreRol, ':codrol' => $codrol])->fetch(PDO::FETCH_ASSOC);

        if($existe){
            $_SESSION['error'] = "Ya existe otro rol con ese nombre.";
            redirect(getUrl('Roles','Roles','editRol', ['id' => $codrol]));
            exit();
        }

        $anterior = $obj->select("SELECT nombrerol FROM tblrol WHERE codrol = :id", [':id' => $codrol])->fetch(PDO::FETCH_ASSOC);

        $obj->update("UPDATE tblrol SET nombrerol = :nombre WHERE codrol = :id", [
            ':nombre' => $nombreRol,
            ':id'     => $codrol,
        ]);

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Roles',
            $codrol,
            $anterior['nombrerol'] ?? null,
            $nombreRol
        );

        $_SESSION['exito'] = "El rol se actualizó correctamente.";
        redirect(getUrl('Roles','Roles','listRol'));
        exit();

    }

    public function activacion()
    {

        $obj = new rolesModel();

        $id = $_GET['id'];
        $estado = $_GET['estado'];

        $sql = "";
        if ($estado === 'A') {
            $sql = "UPDATE tblrol SET estado = 'I' WHERE codrol = :id";
        } else {
            $sql = "UPDATE tblrol SET estado = 'A' WHERE codrol = :id";
        }

        $execute = $obj->update($sql, [":id" => $id]);

        if ($execute) {

            $this->registrarBitacora(
                $obj,
                'UPDATE',
                'Roles',
                $id,
                $estado === 'A' ? 'Activo' : 'Inactivo',
                $estado === 'A' ? 'Inactivo' : 'Activo'
            );

            $_SESSION['exito'] = "Se cambio el estado del rol correctamente.";
            redirect(getUrl('Roles', 'Roles', 'listRol'));
            exit();
        } else {
            $_SESSION['error'] = "No se cambio el estado del rol correctamente.";
            redirect(getUrl('Roles', 'Roles', 'listRol'));
            exit();
        }
    }

    // ---------------------------------------------------------------
    // PERMISOS POR ROL (tblmodulo -> tblcontrolador -> tblfuncion -> tblaccion -> tblrolaccion)
    // ---------------------------------------------------------------

    public function permisos(){

        $obj = new rolesModel();

        $id = $_GET['id'] ?? null;

        if(empty($id)){
            $_SESSION['error'] = "Rol no válido.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        $rol = $obj->select("SELECT codrol, nombrerol FROM tblrol WHERE codrol = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$rol){
            $_SESSION['error'] = "El rol no existe.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        $sql = "SELECT m.nombremodulo, p.codpermiso, p.nombrepermiso, a.codaccion,
                       (ra.codrol IS NOT NULL) AS seleccionado
                FROM tblmodulo m
                CROSS JOIN tblpermiso p
                INNER JOIN tblaccion a ON a.codmodulo = m.codmodulo AND a.codpermiso = p.codpermiso
                LEFT JOIN tblrolaccion ra ON ra.codaccion = a.codaccion AND ra.codrol = :codrol
                ORDER BY m.nombremodulo ASC, p.codpermiso ASC";

        $filas = $obj->select($sql, [':codrol' => $id])->fetchAll(PDO::FETCH_ASSOC);

        // Lista de permisos (columnas) en orden fijo
        $permisosCols = $obj->select("SELECT codpermiso, nombrepermiso FROM tblpermiso ORDER BY codpermiso ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Agrupamos por módulo -> [codpermiso => fila] para pintar la matriz
        $matriz = [];
        foreach($filas as $fila){
            $matriz[$fila['nombremodulo']][$fila['codpermiso']] = $fila;
        }

        include_once __DIR__ . '/../../../view/Roles/permisos.php';

    }

    public function postGuardarPermisos(){

        $obj = new rolesModel();

        $codrol = $_POST['codrol'] ?? null;
        $seleccionadas = $_POST['acciones'] ?? [];

        if(empty($codrol)){
            $_SESSION['error'] = "Rol no válido.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        $conexion = $obj->getConnection();

        try{

            $conexion->beginTransaction();

            $obj->delete("DELETE FROM tblrolaccion WHERE codrol = :codrol", [':codrol' => $codrol]);

            $sqlInsert = "INSERT INTO tblrolaccion (codrol, codaccion) VALUES (:codrol, :codaccion)";
            foreach($seleccionadas as $codaccion){
                $obj->insert($sqlInsert, [
                    ':codrol'    => $codrol,
                    ':codaccion' => (int)$codaccion,
                ]);
            }

            $conexion->commit();

        }catch(\Throwable $error){
            $conexion->rollBack();
            error_log("Error guardando permisos del rol: " . $error->getMessage());
            $_SESSION['error'] = "No se pudieron guardar los permisos. Intenta nuevamente.";
            redirect(getUrl('Roles','Roles','permisos', ['id' => $codrol]));
            exit();
        }

        $this->registrarBitacora($obj, 'UPDATE', 'Roles', $codrol, null, "Permisos actualizados (" . count($seleccionadas) . " acciones)");

        $_SESSION['exito'] = "Los permisos del rol se actualizaron correctamente.";
        redirect(getUrl('Roles','Roles','permisos', ['id' => $codrol]));
        exit();

    }

    public function filtro()
    {

        $obj = new rolesModel();

        $buscar = $_GET['buscar'];

        $sql = "SELECT nombrerol
                FROM tblrol
                WHERE nombreusuario ILIKE :buscar";

        $Roles = $obj->select($sql, [
            ':buscar' => "%$buscar%"
        ]);

        include_once __DIR__ . '/../../../view/Roles/filtro.php';
    }

}