<?php

namespace BioGuppy\Controller\Roles;

use BioGuppy\Model\Roles\rolesModel;
use PDO;

class RolesController{

    private function registrarBitacora($obj, $accion, $modulo, $idregistro = null, $valoranterior = null, $valornuevo = null){

        $codusuario = $_SESSION['usu_id'] ?? null;

        if(empty($codusuario)){
            return;
        }

        $sql = "CALL sp_registrar_bitacora(:codusuario, :accion, :modulo, :idregistro, :valoranterior, :valornuevo)";

        try{
            $obj->insert($sql, [
                ':codusuario'    => $codusuario,
                ':accion'        => $accion,
                ':modulo'        => $modulo,
                ':idregistro'    => $idregistro,
                ':valoranterior' => $valoranterior,
                ':valornuevo'    => $valornuevo,
            ]);
        }catch(\Throwable $error){
            error_log("No se pudo registrar en bitácora: " . $error->getMessage());
        }

    }

    public function createRol(){
        include_once __DIR__ . '/../../../view/Roles/createRol.php';
    }

    public function postcreateRol(){

        $obj = new rolesModel();

        $nombreRol = $_POST['nombreRol'] ?? '';
        $descripcionRol = $_POST['descripcionRol'] ?? '';
        // $_POST['permisos'] llega del formulario pero no se guarda: no hay tabla de permisos.

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