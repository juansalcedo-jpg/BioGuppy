<?php

    namespace BioGuppy\Controller\Tanques;

    use BioGuppy\Model\Tanques\TanquesModel;
    use PDO;

    class TanquesController{

        private function consultarSeguro($obj, $sql, $params = []){
            try{
                return $obj->select($sql, $params);
            }catch(\Throwable $error){
                error_log("Consulta fallida en TanquesController: " . $error->getMessage());
                return false;
            }
        }

        // listado principal
        public function listTan(){

            $obj = new TanquesModel();

            $sql = "SELECT t.codtanque AS id,
                           t.numerotanque AS numero,
                           tt.nombretipotanque AS tipo,
                           t.capacidad,
                           z.nombrezoocriadero AS zoocriadero,
                           CASE WHEN t.estado = 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado
                    FROM tblzootanque t
                    JOIN tbltipotanque tt ON tt.codtipotanque = t.codtipotanque
                    JOIN tblzoocriadero z ON z.codzoocriadero = t.codzoocriadero
                    ORDER BY z.nombrezoocriadero ASC, t.numerotanque ASC";

            $tanques = $this->consultarSeguro($obj, $sql);

            include_once __DIR__ . '/../../../view/Tanques/ListTan.php';

        }

        // formulario de registro
        public function create(){

            $obj = new TanquesModel();

            $tiposTanque = $this->consultarSeguro($obj,
                "SELECT codtipotanque, nombretipotanque
                 FROM tbltipotanque
                 WHERE estado = 'A'
                 ORDER BY nombretipotanque ASC");

            $zoocriaderos = $this->consultarSeguro($obj,
                "SELECT codzoocriadero, nombrezoocriadero
                 FROM tblzoocriadero
                 WHERE estado = 'A'
                 ORDER BY nombrezoocriadero ASC");

            include_once __DIR__ . '/../../../view/Tanques/createTan.php';

        }

        // valida y crea un nuevo tanque
        public function postCreateTan(){

            $obj = new TanquesModel();

            $numero        = trim($_POST['numero_tanque'] ?? '');
            $codTipoTanque = $_POST['codtipotanque'] ?? '';
            $capacidad     = $_POST['capacidad'] ?? '';
            $codZoocriadero = $_POST['codzoocriadero'] ?? '';
            $estado        = isset($_POST['estado_tanque']) ? 'A' : 'I';

            if(empty($numero) || empty($codTipoTanque) || empty($capacidad) || empty($codZoocriadero)){
                $_SESSION['error'] = "Número de tanque, tipo, capacidad y zoocriadero son obligatorios.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            if(!is_numeric($numero) || $numero <= 0){
                $_SESSION['error'] = "El número de tanque debe ser un número entero (ej. 6), sin letras ni guiones.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            if(!is_numeric($capacidad) || $capacidad <= 0){
                $_SESSION['error'] = "La capacidad debe ser un número mayor a cero.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            try{

                // valida que no exista ya ese numero de tanque en el mismo zoocriadero (UNIQUE en BD)
                $sqlValidar = "SELECT codtanque FROM tblzootanque
                               WHERE numerotanque = :numero AND codzoocriadero = :codzoocriadero";
                $existe = $obj->select($sqlValidar, [
                    ':numero' => $numero,
                    ':codzoocriadero' => $codZoocriadero,
                ])->fetch(PDO::FETCH_ASSOC);

                if($existe){
                    $_SESSION['error'] = "Ya existe un tanque con ese número en ese zoocriadero.";
                    redirect(getUrl('Tanques','Tanques','create'));
                    exit();
                }

                $sql = "INSERT INTO public.tblzootanque
                            (codtanque, codzoocriadero, codtipotanque, numerotanque, capacidad, fechacreacion, estado)
                        VALUES
                            (DEFAULT, :codzoocriadero, :codtipotanque, :numero, :capacidad, DEFAULT, :estado)";

                $obj->insert($sql, [
                    ':codzoocriadero' => $codZoocriadero,
                    ':codtipotanque'  => $codTipoTanque,
                    ':numero'         => $numero,
                    ':capacidad'      => $capacidad,
                    ':estado'         => $estado,
                ]);

            }catch(\Throwable $error){
                error_log("Error al registrar tanque: " . $error->getMessage());
                $_SESSION['error'] = "No se pudo registrar el tanque. Verifica los datos e intenta de nuevo.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            $_SESSION['exito'] = "El tanque se registró exitosamente.";
            redirect(getUrl('Tanques','Tanques','listTan'));
            exit();

        }

        // formulario de edicion
        public function getUpdate(){

            $obj = new TanquesModel();

            $id = $_GET['id'] ?? null;

            if(empty($id)){
                $_SESSION['error'] = "Tanque no válido.";
                redirect(getUrl('Tanques','Tanques','listTan'));
                exit();
            }

            $sql = "SELECT codtanque, codzoocriadero, codtipotanque, numerotanque, capacidad, estado
                    FROM tblzootanque
                    WHERE codtanque = :id";

            $tanque = $this->consultarSeguro($obj, $sql, [':id' => $id]);

            if(!$tanque || $tanque->rowCount() === 0){
                $_SESSION['error'] = "Tanque no válido.";
                redirect(getUrl('Tanques','Tanques','listTan'));
                exit();
            }

            $tiposTanque = $this->consultarSeguro($obj,
                "SELECT codtipotanque, nombretipotanque
                 FROM tbltipotanque
                 WHERE estado = 'A'
                 ORDER BY nombretipotanque ASC");

            $zoocriaderos = $this->consultarSeguro($obj,
                "SELECT codzoocriadero, nombrezoocriadero
                 FROM tblzoocriadero
                 WHERE estado = 'A'
                 ORDER BY nombrezoocriadero ASC");

            include_once __DIR__ . '/../../../view/Tanques/getUpdateTan.php';

        }

        // guardar edicion
        public function postUpdateTan(){

            $obj = new TanquesModel();

            $id             = $_POST['codtanque'] ?? null;
            $numero         = trim($_POST['numero_tanque'] ?? '');
            $codTipoTanque  = $_POST['codtipotanque'] ?? '';
            $capacidad      = $_POST['capacidad'] ?? '';
            $codZoocriadero = $_POST['codzoocriadero'] ?? '';
            $estado         = isset($_POST['estado_tanque']) ? 'A' : 'I';

            if(empty($id)){
                $_SESSION['error'] = "Tanque no válido.";
                redirect(getUrl('Tanques','Tanques','listTan'));
                exit();
            }

            if(empty($numero) || empty($codTipoTanque) || empty($capacidad) || empty($codZoocriadero)){
                $_SESSION['error'] = "Número de tanque, tipo, capacidad y zoocriadero son obligatorios.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            if(!is_numeric($numero) || $numero <= 0){
                $_SESSION['error'] = "El número de tanque debe ser un número entero (ej. 6), sin letras ni guiones.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            if(!is_numeric($capacidad) || $capacidad <= 0){
                $_SESSION['error'] = "La capacidad debe ser un número mayor a cero.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            try{

                // valida que no exista OTRO tanque con ese mismo numero en el mismo zoocriadero
                $sqlValidar = "SELECT codtanque FROM tblzootanque
                               WHERE numerotanque = :numero AND codzoocriadero = :codzoocriadero
                               AND codtanque != :id";
                $existe = $obj->select($sqlValidar, [
                    ':numero' => $numero,
                    ':codzoocriadero' => $codZoocriadero,
                    ':id' => $id,
                ])->fetch(PDO::FETCH_ASSOC);

                if($existe){
                    $_SESSION['error'] = "Ya existe otro tanque con ese número en ese zoocriadero.";
                    redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                    exit();
                }

                $sql = "UPDATE tblzootanque
                        SET codzoocriadero = :codzoocriadero,
                            codtipotanque  = :codtipotanque,
                            numerotanque   = :numero,
                            capacidad      = :capacidad,
                            estado         = :estado
                        WHERE codtanque = :id";

                $obj->update($sql, [
                    ':codzoocriadero' => $codZoocriadero,
                    ':codtipotanque'  => $codTipoTanque,
                    ':numero'         => $numero,
                    ':capacidad'      => $capacidad,
                    ':estado'         => $estado,
                    ':id'             => $id,
                ]);

            }catch(\Throwable $error){
                error_log("Error al editar tanque: " . $error->getMessage());
                $_SESSION['error'] = "No se pudo actualizar el tanque. Verifica los datos e intenta de nuevo.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            $_SESSION['exito'] = "El tanque se actualizó correctamente.";
            redirect(getUrl('Tanques','Tanques','listTan'));
            exit();

        }

        // inhabilitar y habilitar
public function delete(){

    $obj = new TanquesModel();

    $id = $_GET['id'] ?? null;

    if(empty($id)){
        $_SESSION['error'] = "Tanque no válido.";
        redirect(getUrl('Tanques','Tanques','listTan'));
        exit();
    }

    $actual = $obj->select("SELECT estado FROM tblzootanque WHERE codtanque = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);

    if(!$actual){
        $_SESSION['error'] = "Tanque no válido.";
        redirect(getUrl('Tanques','Tanques','listTan'));
        exit();
    }

    $nuevoEstado = ($actual['estado'] === 'A') ? 'I' : 'A';

    $obj->update("UPDATE tblzootanque SET estado = :estado WHERE codtanque = :id", [
        ':estado' => $nuevoEstado,
        ':id' => $id,
    ]);

    $_SESSION['exito'] = "El estado del tanque se actualizó correctamente.";
    redirect(getUrl('Tanques','Tanques','listTan'));
    exit();

}

        // buscador
        public function filtro(){

            $obj = new TanquesModel();

            $buscar = $_GET['buscar'] ?? '';

            $sql = "SELECT t.codtanque AS id,
                           t.numerotanque AS numero,
                           tt.nombretipotanque AS tipo,
                           t.capacidad,
                           z.nombrezoocriadero AS zoocriadero,
                           CASE WHEN t.estado = 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado
                    FROM tblzootanque t
                    JOIN tbltipotanque tt ON tt.codtipotanque = t.codtipotanque
                    JOIN tblzoocriadero z ON z.codzoocriadero = t.codzoocriadero
                    WHERE CAST(t.numerotanque AS TEXT) ILIKE :buscar
                       OR tt.nombretipotanque ILIKE :buscar
                       OR z.nombrezoocriadero ILIKE :buscar
                    ORDER BY z.nombrezoocriadero ASC, t.numerotanque ASC";

            $tanques = $this->consultarSeguro($obj, $sql, [':buscar' => "%$buscar%"]);

            include_once __DIR__ . '/../../../view/Tanques/filtroTan.php';

        }
    }

?>
