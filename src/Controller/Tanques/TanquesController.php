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

        private function registrarBitacora($obj, $accion, $modulo, $idregistro = null, $valoranterior = null, $valornuevo = null){

            $codusuario = $_SESSION['usu_id'] ?? null;

            if(empty($codusuario)){
                return; // si no hay sesión activa, no se registra nada
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

            // Próximo número de tanque disponible por cada zoocriadero
            // (se muestra en el formulario, pero el que realmente se
            // guarda siempre se recalcula en el servidor).
            $zoocriaderos = $this->consultarSeguro($obj,
                "SELECT z.codzoocriadero, z.nombrezoocriadero,
                        COALESCE(MAX(t.numerotanque), 0) + 1 AS siguiente_numero
                 FROM tblzoocriadero z
                 LEFT JOIN tblzootanque t ON t.codzoocriadero = z.codzoocriadero
                 WHERE z.estado = 'A'
                 GROUP BY z.codzoocriadero, z.nombrezoocriadero
                 ORDER BY z.nombrezoocriadero ASC");

            include_once __DIR__ . '/../../../view/Tanques/createTan.php';

        }

        // valida y crea un nuevo tanque
        public function postCreateTan(){

            $obj = new TanquesModel();

            $codTipoTanque = $_POST['codtipotanque'] ?? '';
            $capacidadTexto = trim($_POST['capacidad'] ?? '');
            $codZoocriadero = $_POST['codzoocriadero'] ?? '';
            // El estado ya no se pide en el formulario: todo tanque nuevo
            // se crea Activo; el estado se maneja únicamente con el botón
            // Inhabilitar de la lista.
            $estado        = 'A';

            if(empty($codTipoTanque) || empty($capacidadTexto) || empty($codZoocriadero)){
                $_SESSION['error'] = "Tipo, capacidad y zoocriadero son obligatorios.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            // -----------------------------------------------------------
            // VALIDAR FORMATO DE CAPACIDAD (litros)
            // Solo números, con una coma opcional para decimales
            // (ej. 20 ó 20,5). No se permiten letras ni puntos.
            // -----------------------------------------------------------
            if(!preg_match('/^\d+(,\d{1,2})?$/', $capacidadTexto)){
                $_SESSION['error'] = "La capacidad solo puede llevar números y, si aplica, una coma para decimales (ej: 20,5).";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            $capacidad = (float) str_replace(',', '.', $capacidadTexto);

            // -----------------------------------------------------------
            // RANGO REALISTA DE CAPACIDAD (litros)
            // -----------------------------------------------------------
            $capacidadMinima = 5;
            $capacidadMaxima = 1000;

            if($capacidad < $capacidadMinima || $capacidad > $capacidadMaxima){
                $_SESSION['error'] = "La capacidad debe estar entre {$capacidadMinima} y {$capacidadMaxima} litros.";
                redirect(getUrl('Tanques','Tanques','create'));
                exit();
            }

            try{

                // -----------------------------------------------------------
                // EL NÚMERO DE TANQUE NUNCA SE TOMA DEL FORMULARIO:
                // siempre es el siguiente consecutivo dentro del
                // zoocriadero seleccionado, para que no se puedan crear
                // huecos ni números arbitrarios.
                // -----------------------------------------------------------
                $sqlSiguiente = "SELECT COALESCE(MAX(numerotanque), 0) + 1 AS siguiente
                                  FROM tblzootanque
                                  WHERE codzoocriadero = :codzoocriadero";

                $siguiente = $obj->select($sqlSiguiente, [
                    ':codzoocriadero' => $codZoocriadero,
                ])->fetch(PDO::FETCH_ASSOC);

                $numero = $siguiente['siguiente'];

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

                // AUDITORÍA: buscamos el id recién creado
                $nuevo = $obj->select(
                    "SELECT codtanque FROM tblzootanque
                     WHERE codzoocriadero = :codzoocriadero AND numerotanque = :numero",
                    [':codzoocriadero' => $codZoocriadero, ':numero' => $numero]
                )->fetch(PDO::FETCH_ASSOC);

                $this->registrarBitacora(
                    $obj,
                    'INSERT',
                    'Tanques',
                    $nuevo['codtanque'] ?? null,
                    null,
                    'Tanque #' . $numero
                );

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

            // Próximo número disponible por zoocriadero, para el caso en que
            // el usuario cambie el tanque de zoocriadero durante la edición.
            $zoocriaderos = $this->consultarSeguro($obj,
                "SELECT z.codzoocriadero, z.nombrezoocriadero,
                        COALESCE(MAX(t.numerotanque), 0) + 1 AS siguiente_numero
                 FROM tblzoocriadero z
                 LEFT JOIN tblzootanque t ON t.codzoocriadero = z.codzoocriadero
                 WHERE z.estado = 'A'
                 GROUP BY z.codzoocriadero, z.nombrezoocriadero
                 ORDER BY z.nombrezoocriadero ASC");

            include_once __DIR__ . '/../../../view/Tanques/getUpdateTan.php';

        }

        // guardar edicion
        public function postUpdateTan(){

            $obj = new TanquesModel();

            $id             = $_POST['codtanque'] ?? null;
            $codTipoTanque  = $_POST['codtipotanque'] ?? '';
            $capacidadTexto = trim($_POST['capacidad'] ?? '');
            $codZoocriadero = $_POST['codzoocriadero'] ?? '';
            // El estado ya no se edita desde este formulario: se maneja
            // únicamente con el botón Inhabilitar de la lista, así que
            // la edición nunca lo modifica.

            if(empty($id)){
                $_SESSION['error'] = "Tanque no válido.";
                redirect(getUrl('Tanques','Tanques','listTan'));
                exit();
            }

            if(empty($codTipoTanque) || empty($capacidadTexto) || empty($codZoocriadero)){
                $_SESSION['error'] = "Tipo, capacidad y zoocriadero son obligatorios.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            // -----------------------------------------------------------
            // VALIDAR FORMATO DE CAPACIDAD (litros) - igual que al crear
            // -----------------------------------------------------------
            if(!preg_match('/^\d+(,\d{1,2})?$/', $capacidadTexto)){
                $_SESSION['error'] = "La capacidad solo puede llevar números y, si aplica, una coma para decimales (ej: 20,5).";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            $capacidad = (float) str_replace(',', '.', $capacidadTexto);

            $capacidadMinima = 5;
            $capacidadMaxima = 1000;

            if($capacidad < $capacidadMinima || $capacidad > $capacidadMaxima){
                $_SESSION['error'] = "La capacidad debe estar entre {$capacidadMinima} y {$capacidadMaxima} litros.";
                redirect(getUrl('Tanques','Tanques','getUpdate',['id'=>$id]));
                exit();
            }

            try{

                // -----------------------------------------------------------
                // EL NÚMERO DE TANQUE NUNCA SE TOMA DEL FORMULARIO.
                // Si el zoocriadero no cambió, se conserva el número que
                // ya tenía. Si cambió, se le asigna el siguiente
                // consecutivo disponible en el nuevo zoocriadero.
                // -----------------------------------------------------------
                $actual = $obj->select(
                    "SELECT numerotanque, codzoocriadero FROM tblzootanque WHERE codtanque = :id",
                    [':id' => $id]
                )->fetch(PDO::FETCH_ASSOC);

                if(!$actual){
                    $_SESSION['error'] = "Tanque no válido.";
                    redirect(getUrl('Tanques','Tanques','listTan'));
                    exit();
                }

                if((string) $actual['codzoocriadero'] === (string) $codZoocriadero){

                    $numero = $actual['numerotanque'];

                } else {

                    $sqlSiguiente = "SELECT COALESCE(MAX(numerotanque), 0) + 1 AS siguiente
                                      FROM tblzootanque
                                      WHERE codzoocriadero = :codzoocriadero";

                    $siguiente = $obj->select($sqlSiguiente, [
                        ':codzoocriadero' => $codZoocriadero,
                    ])->fetch(PDO::FETCH_ASSOC);

                    $numero = $siguiente['siguiente'];

                }

                $sql = "UPDATE tblzootanque
                        SET codzoocriadero = :codzoocriadero,
                            codtipotanque  = :codtipotanque,
                            numerotanque   = :numero,
                            capacidad      = :capacidad
                        WHERE codtanque = :id";

                $obj->update($sql, [
                    ':codzoocriadero' => $codZoocriadero,
                    ':codtipotanque'  => $codTipoTanque,
                    ':numero'         => $numero,
                    ':capacidad'      => $capacidad,
                    ':id'             => $id,
                ]);

                $this->registrarBitacora(
                    $obj,
                    'UPDATE',
                    'Tanques',
                    $id,
                    'Tanque #' . $actual['numerotanque'],
                    'Tanque #' . $numero
                );

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

    $this->registrarBitacora(
        $obj,
        'UPDATE',
        'Tanques',
        $id,
        $actual['estado'] === 'A' ? 'Activo' : 'Inactivo',
        $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
    );

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