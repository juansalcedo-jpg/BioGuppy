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

        // -----------------------------------------------------------
        // VALIDAR FORMATO DE NOMBRE
        // Solo letras y espacios, máximo 50 caracteres.
        // -----------------------------------------------------------
        if(mb_strlen(trim($nombreRol)) > 50){
            $_SESSION['error'] = "El nombre del rol no puede tener más de 50 caracteres.";
            redirect(getUrl('Roles','Roles','createRol'));
            exit();
        }

        if(!preg_match('/^[\p{L} ]+$/u', trim($nombreRol))){
            $_SESSION['error'] = "El nombre del rol solo puede contener letras y espacios (sin números, símbolos ni puntuación).";
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

    // Consulta de roles con cuántos módulos tiene cada uno.
    private function consultarRoles($obj, $buscar = null){

        $sql = "SELECT r.codrol, r.nombrerol, r.estado,
                       COUNT(m.codmodulo) AS totalmodulos
                FROM tblrol r
                LEFT JOIN tblrolmodulo rm ON rm.codrol = r.codrol
                LEFT JOIN tblmodulo m     ON m.codmodulo = rm.codmodulo AND m.estado = 'A'";

        $params = [];
        if($buscar !== null && $buscar !== ''){
            $sql .= " WHERE r.nombrerol ILIKE :buscar";
            $params[':buscar'] = "%$buscar%";
        }

        $sql .= " GROUP BY r.codrol, r.nombrerol, r.estado
                  ORDER BY r.codrol ASC";

        return $obj->select($sql, $params);
    }

    public function listRol(){

        $obj = new rolesModel();

        $resultrol = $this->consultarRoles($obj);

        // Para mostrar "de N" en la columna de módulos.
        $totalModulosSistema = (int) $obj->select("SELECT COUNT(*) FROM tblmodulo WHERE estado = 'A'")->fetchColumn();

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

        if(mb_strlen(trim($nombreRol)) > 50){
            $_SESSION['error'] = "El nombre del rol no puede tener más de 50 caracteres.";
            redirect(getUrl('Roles','Roles','editRol', ['id' => $codrol]));
            exit();
        }

        if(!preg_match('/^[\p{L} ]+$/u', trim($nombreRol))){
            $_SESSION['error'] = "El nombre del rol solo puede contener letras y espacios (sin números, símbolos ni puntuación).";
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

        // -----------------------------------------------------------
        // INTEGRIDAD: no permitir inhabilitar un rol si todavía tiene
        // usuarios activos asignados (quedarían con un rol "fantasma").
        // -----------------------------------------------------------
        if ($estado === 'A') {
            $usuariosActivos = $obj->select(
                "SELECT COUNT(*) AS total FROM tblusuario WHERE codrol = :id AND estado = 'A'",
                [':id' => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (($usuariosActivos['total'] ?? 0) > 0) {
                $_SESSION['error'] = "No se puede inhabilitar este rol porque tiene " . $usuariosActivos['total'] . " usuario(s) activo(s) asignado(s). Reasígnalos a otro rol primero.";
                redirect(getUrl('Roles', 'Roles', 'listRol'));
                exit();
            }
        }

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


    /*
     * Pantalla de permisos: un interruptor por módulo.
     * Si el rol tiene el módulo, ve todo lo de ese módulo; si no, no lo ve
     * en la barra lateral ni puede entrar por URL.
     */
    public function permisos(){

        $obj = new rolesModel();

        $id = (int) ($_GET['id'] ?? 0);

        $rol = $obj->select("SELECT codrol, nombrerol, estado FROM tblrol WHERE codrol = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        if(!$rol){
            $_SESSION['error'] = "El rol no existe.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        $sql = "SELECT m.codmodulo, m.nombremodulo, m.carpeta, m.descripcion, m.icono, m.seccion,
                       (rm.codrol IS NOT NULL) AS asignado
                FROM tblmodulo m
                LEFT JOIN tblrolmodulo rm ON rm.codmodulo = m.codmodulo AND rm.codrol = :codrol
                WHERE m.estado = 'A'
                ORDER BY m.orden, m.nombremodulo";

        $filas = $obj->select($sql, [':codrol' => $id])->fetchAll(PDO::FETCH_ASSOC);

        // Agrupados por sección, igual que en la barra lateral.
        $modulosPorSeccion = [];
        $totalAsignados = 0;
        foreach($filas as $fila){
            $fila['asignado'] = (bool) $fila['asignado'];
            if($fila['asignado']){
                $totalAsignados++;
            }
            $modulosPorSeccion[$fila['seccion']][] = $fila;
        }
        $totalModulos = count($filas);

        // Si estoy editando MI propio rol, no me dejo quitar "Roles"
        // (me quedaría por fuera de esta misma pantalla).
        $esMiRol = ((int) ($_SESSION['codrol'] ?? 0) === (int) $rol['codrol']);

        include_once __DIR__ . '/../../../view/Roles/permisos.php';

    }

    public function postGuardarPermisos(){

        $obj = new rolesModel();

        $codrol = (int) ($_POST['codrol'] ?? 0);

        // Solo enteros, sin repetidos.
        $seleccionados = array_values(array_unique(array_filter(
            array_map('intval', (array) ($_POST['modulos'] ?? [])),
            function($cod){ return $cod > 0; }
        )));

        $rol = $obj->select("SELECT codrol, nombrerol FROM tblrol WHERE codrol = :id", [':id' => $codrol])->fetch(PDO::FETCH_ASSOC);

        if(!$rol){
            $_SESSION['error'] = "Rol no válido.";
            redirect(getUrl('Roles','Roles','listRol'));
            exit();
        }

        // Módulos válidos (existentes y activos) con su nombre, para validar y para la bitácora.
        $modulosValidos = $obj->select(
            "SELECT codmodulo, nombremodulo, LOWER(carpeta) AS carpeta FROM tblmodulo WHERE estado = 'A'"
        )->fetchAll(PDO::FETCH_ASSOC);

        $nombrePorCod = [];
        $codRoles = null;
        foreach($modulosValidos as $m){
            $nombrePorCod[(int) $m['codmodulo']] = $m['nombremodulo'];
            if($m['carpeta'] === 'roles'){
                $codRoles = (int) $m['codmodulo'];
            }
        }

        // Se descarta cualquier código que no sea un módulo real.
        $seleccionados = array_values(array_filter($seleccionados, function($cod) use ($nombrePorCod){
            return isset($nombrePorCod[$cod]);
        }));

        // Protección: no te puedes quitar "Roles" a ti mismo.
        $esMiRol = ((int) ($_SESSION['codrol'] ?? 0) === $codrol);
        if($esMiRol && $codRoles !== null && !in_array($codRoles, $seleccionados, true)){
            $seleccionados[] = $codRoles;
        }

        $anteriores = array_map('intval', $obj->select(
            "SELECT codmodulo FROM tblrolmodulo WHERE codrol = :codrol",
            [':codrol' => $codrol]
        )->fetchAll(PDO::FETCH_COLUMN));

        $conexion = $obj->getConnection();

        try{

            $conexion->beginTransaction();

            $obj->delete("DELETE FROM tblrolmodulo WHERE codrol = :codrol", [':codrol' => $codrol]);

            $sqlInsert = "INSERT INTO tblrolmodulo (codrol, codmodulo) VALUES (:codrol, :codmodulo)";
            foreach($seleccionados as $codmodulo){
                $obj->insert($sqlInsert, [
                    ':codrol'    => $codrol,
                    ':codmodulo' => $codmodulo,
                ]);
            }

            $conexion->commit();

        }catch(\Throwable $error){
            if($conexion->inTransaction()){
                $conexion->rollBack();
            }
            error_log("Error guardando permisos del rol: " . $error->getMessage());
            $_SESSION['error'] = "No se pudieron guardar los permisos. Intenta nuevamente.";
            redirect(getUrl('Roles','Roles','permisos', ['id' => $codrol]));
            exit();
        }

        // Bitácora: nombres de los módulos antes y después.
        $nombres = function($cods) use ($nombrePorCod){
            $lista = [];
            foreach($cods as $c){
                if(isset($nombrePorCod[$c])){
                    $lista[] = $nombrePorCod[$c];
                }
            }
            sort($lista);
            return empty($lista) ? 'Sin módulos' : implode(', ', $lista);
        };

        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Roles',
            $codrol,
            "Módulos: " . $nombres($anteriores),
            "Módulos: " . $nombres($seleccionados)
        );

        $_SESSION['exito'] = "Se guardaron los permisos del rol " . $rol['nombrerol'] . ". Los cambios aplican de inmediato.";
        redirect(getUrl('Roles','Roles','permisos', ['id' => $codrol]));
        exit();

    }

    public function filtro()
    {

        $obj = new rolesModel();

        $buscar = trim($_GET['buscar'] ?? '');

        $Roles = $this->consultarRoles($obj, $buscar);

        $totalModulosSistema = (int) $obj->select("SELECT COUNT(*) FROM tblmodulo WHERE estado = 'A'")->fetchColumn();

        include_once __DIR__ . '/../../../view/Roles/filtro.php';
    }

}