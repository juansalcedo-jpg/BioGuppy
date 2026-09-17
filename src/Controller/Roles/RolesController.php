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

        $sql = "INSERT INTO tblrol (nombrerol, descripcionrol)
                VALUES (:nombre, :descripcion)
                RETURNING codrol";

        $stmt = $obj->insert($sql, [
            ':nombre' => $nombreRol,
            ':descripcion' => trim($descripcionRol) !== '' ? $descripcionRol : null,
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

        $sql = "SELECT codrol, nombrerol, descripcionrol FROM tblrol ORDER BY codrol ASC";

        $resultrol = $obj->select($sql);

        include_once __DIR__ . '/../../../view/Roles/listRol.php';

    }

}