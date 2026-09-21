<?php

namespace BioGuppy\Controller\Zoocriadero;

use BioGuppy\Model\ZoocriaderoCor\Zoocriaderocor;
use PDO;

class ZoocriaderoController
{

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

    // ---------------------------------------------------------------
    // LISTADO PRINCIPAL
    // ---------------------------------------------------------------
    public function listZoo()
    {
        $obj = new Zoocriaderocor();

        $sql = "SELECT
                    z.codzoocriadero AS id,
                    z.nombrezoocriadero AS nombre,
                    z.direccion,
                    c.nombrecomuna AS comuna,
                    b.nombrebarrio AS barrio,
                    u.nombreusuario || ' ' || u.apellidousuario AS encargado,
                    z.estado

                FROM tblzoocriadero z

                INNER JOIN tblbarrio b
                    ON b.codbarrio = z.codbarrio

                INNER JOIN tblcomuna c
                    ON c.codcomuna = b.codcomuna

                INNER JOIN tblusuario u
                    ON u.codusuario = z.codusuario

                ORDER BY z.codzoocriadero ASC";

        $zoocriaderos = $obj->select($sql);

        include_once __DIR__
            . '/../../../view/Zoocriadero/listZoo.php';
    }


   
    // FORMULARIO CREAR
    
    public function create()
    {
        $obj = new Zoocriaderocor();


        // COMUNAS
        $sqlComunas = "SELECT
                            codcomuna,
                            nombrecomuna
                       FROM tblcomuna
                       WHERE estado = 'A'
                       ORDER BY codcomuna ASC";

        $comunas = $obj->select($sqlComunas);


        // BARRIOS
        $sqlBarrios = "SELECT
                            codbarrio,
                            codcomuna,
                            nombrebarrio
                       FROM tblbarrio
                       WHERE estado = 'A'
                       ORDER BY nombrebarrio ASC";

        $barrios = $obj->select($sqlBarrios);


        // AUXILIARES ZOOCRIADERO
        $sqlAuxiliares = "SELECT
                                u.codusuario,
                                u.nombreusuario,
                                u.apellidousuario

                          FROM tblusuario u

                          INNER JOIN tblrol r
                              ON r.codrol = u.codrol

                          WHERE r.nombrerol = 'Auxiliar Zoocriadero'
                          AND u.estado = 'A'
                          AND r.estado = 'A'

                          ORDER BY
                              u.nombreusuario ASC,
                              u.apellidousuario ASC";

        $auxiliares = $obj->select($sqlAuxiliares);


        include_once __DIR__
            . '/../../../view/Zoocriadero/create.php';
    }


    // ---------------------------------------------------------------
    // REGISTRAR
    // ---------------------------------------------------------------
    public function postCreateZoo()
    {
        $obj = new Zoocriaderocor();


        $nombre =
            trim($_POST['nombre'] ?? '');

        $direccion =
            trim($_POST['direccion'] ?? '');

        $codcomuna =
            $_POST['codcomuna'] ?? '';

        $codbarrio =
            $_POST['codbarrio'] ?? '';

        // AQUÍ LLEGA EL CODUSUARIO DEL AUXILIAR
        $codusuarioAuxiliar =
            $_POST['encargado'] ?? '';

        // El estado ya no se pide en el formulario: todo registro nuevo
        // se crea Activo. El estado se maneja únicamente con el botón
        // Habilitar/Inhabilitar de la lista.
        $estado = 'A';


        // -----------------------------------------------------------
        // VALIDAR CAMPOS
        // -----------------------------------------------------------
        if (
            empty($nombre) ||
            empty($direccion) ||
            empty($codcomuna) ||
            empty($codbarrio) ||
            empty($codusuarioAuxiliar)
        ) {

            $_SESSION['error'] =
                "Todos los campos son obligatorios.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR FORMATO DE NOMBRE
        // Solo letras y espacios (sin números, símbolos ni puntuación),
        // máximo 80 caracteres.
        // -----------------------------------------------------------
        if (mb_strlen($nombre) > 80) {

            $_SESSION['error'] =
                "El nombre no puede tener más de 80 caracteres.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }

        if (!preg_match('/^[\p{L} ]+$/u', $nombre)) {

            $_SESSION['error'] =
                "El nombre solo puede contener letras y espacios (sin números, símbolos ni puntuación).";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR FORMATO DE DIRECCIÓN
        // Debe iniciar con la nomenclatura vial usada en Cali:
        // Calle, Carrera o Avenida.
        // -----------------------------------------------------------
        if (!preg_match('/^(Calle|Carrera|Avenida)\b/iu', $direccion)) {

            $_SESSION['error'] =
                "La dirección debe iniciar con Calle, Carrera o Avenida.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR COMUNA
        // -----------------------------------------------------------
        $sqlComuna = "SELECT codcomuna
                      FROM tblcomuna
                      WHERE codcomuna = :codcomuna
                      AND estado = 'A'";

        $comunaExiste = $obj->select(
            $sqlComuna,
            [
                ':codcomuna' => $codcomuna
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$comunaExiste) {

            $_SESSION['error'] =
                "La comuna seleccionada no es válida.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR BARRIO
        // -----------------------------------------------------------
        $sqlBarrio = "SELECT codbarrio
                      FROM tblbarrio
                      WHERE codbarrio = :codbarrio
                      AND codcomuna = :codcomuna
                      AND estado = 'A'";

        $barrioExiste = $obj->select(
            $sqlBarrio,
            [
                ':codbarrio' => $codbarrio,
                ':codcomuna' => $codcomuna
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$barrioExiste) {

            $_SESSION['error'] =
                "El barrio seleccionado no pertenece a la comuna.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // VALIDAR AUXILIAR
       
        $sqlAuxiliar = "SELECT
                            u.codusuario

                        FROM tblusuario u

                        INNER JOIN tblrol r
                            ON r.codrol = u.codrol

                        WHERE u.codusuario = :codusuario
                        AND r.nombrerol = 'Auxiliar Zoocriadero'
                        AND u.estado = 'A'
                        AND r.estado = 'A'";

        $auxiliarExiste = $obj->select(
            $sqlAuxiliar,
            [
                ':codusuario' => $codusuarioAuxiliar
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$auxiliarExiste) {

            $_SESSION['error'] =
                "El auxiliar seleccionado no es válido.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // VALIDAR NOMBRE REPETIDO
       
        $sqlValidar = "SELECT codzoocriadero
                       FROM tblzoocriadero
                       WHERE nombrezoocriadero ILIKE :nombre";

        $existe = $obj->select(
            $sqlValidar,
            [
                ':nombre' => $nombre
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if ($existe) {

            $_SESSION['error'] =
                "Ya existe un zoocriadero con ese nombre.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // INSERTAR
       
        $sql = "INSERT INTO tblzoocriadero
                (
                    codusuario,
                    codbarrio,
                    nombrezoocriadero,
                    direccion,
                    fechacreacion,
                    estado
                )
                VALUES
                (
                    :codusuario,
                    :codbarrio,
                    :nombre,
                    :direccion,
                    DEFAULT,
                    :estado
                )";


        $obj->insert(
            $sql,
            [
                ':codusuario' => $codusuarioAuxiliar,
                ':codbarrio' => $codbarrio,
                ':nombre' => $nombre,
                ':direccion' => $direccion,
                ':estado' => $estado
            ]
        );


        // AUDITORÍA: buscamos el id recién creado
        $nuevo = $obj->select(
            "SELECT codzoocriadero FROM tblzoocriadero
             WHERE nombrezoocriadero = :nombre AND codbarrio = :codbarrio
             ORDER BY codzoocriadero DESC LIMIT 1",
            [':nombre' => $nombre, ':codbarrio' => $codbarrio]
        )->fetch(PDO::FETCH_ASSOC);

        $this->registrarBitacora(
            $obj,
            'INSERT',
            'Zoocriadero',
            $nuevo['codzoocriadero'] ?? null,
            null,
            $nombre
        );


        $_SESSION['exito'] =
            "El zoocriadero se registró correctamente.";


        redirect(
            getUrl(
                'Zoocriadero',
                'Zoocriadero',
                'listZoo'
            )
        );

        exit();
    }


    // ---------------------------------------------------------------
    // FORMULARIO EDITAR
    // ---------------------------------------------------------------
    public function getUpdate()
    {
        $obj = new Zoocriaderocor();

        $id =
            $_GET['id'] ?? null;


        if (empty($id)) {

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
        }


        $sqlZoo = "SELECT
                        z.codzoocriadero,
                        z.codusuario,
                        z.nombrezoocriadero,
                        z.direccion,
                        z.codbarrio,
                        z.estado,
                        b.codcomuna

                   FROM tblzoocriadero z

                   INNER JOIN tblbarrio b
                       ON b.codbarrio = z.codbarrio

                   WHERE z.codzoocriadero = :id";


        $zoocriadero = $obj->select(
            $sqlZoo,
            [
                ':id' => $id
            ]
        )->fetch(PDO::FETCH_ASSOC);


        // COMUNAS
        $sqlComunas = "SELECT
                            codcomuna,
                            nombrecomuna
                       FROM tblcomuna
                       WHERE estado = 'A'
                       ORDER BY nombrecomuna ASC";

        $comunas = $obj->select($sqlComunas);


        // BARRIOS
        $sqlBarrios = "SELECT
                            codbarrio,
                            codcomuna,
                            nombrebarrio
                       FROM tblbarrio
                       WHERE estado = 'A'
                       ORDER BY nombrebarrio ASC";

        $barrios = $obj->select($sqlBarrios);


        // AUXILIARES
        $sqlAuxiliares = "SELECT
                                u.codusuario,
                                u.nombreusuario,
                                u.apellidousuario

                          FROM tblusuario u

                          INNER JOIN tblrol r
                              ON r.codrol = u.codrol

                          WHERE r.nombrerol = 'Auxiliar Zoocriadero'
                          AND u.estado = 'A'
                          AND r.estado = 'A'

                          ORDER BY
                              u.nombreusuario ASC,
                              u.apellidousuario ASC";

        $auxiliares = $obj->select($sqlAuxiliares);


        include_once __DIR__
            . '/../../../view/Zoocriadero/Getupdatezoo.php';
    }


    // ---------------------------------------------------------------
    // ACTUALIZAR
    // ---------------------------------------------------------------
    public function postUpdateZoo()
    {
        $obj = new Zoocriaderocor();


        $id =
            $_POST['codzoocriadero'] ?? null;

        $nombre =
            trim($_POST['nombre'] ?? '');

        $direccion =
            trim($_POST['direccion'] ?? '');

        $codcomuna =
            $_POST['codcomuna'] ?? '';

        $codbarrio =
            $_POST['codbarrio'] ?? '';

        $codusuarioAuxiliar =
            $_POST['encargado'] ?? '';

        // El estado ya no se edita desde este formulario: se maneja
        // únicamente con el botón Habilitar/Inhabilitar de la lista,
        // así que la edición nunca lo modifica.

        if (
            empty($id) ||
            empty($nombre) ||
            empty($direccion) ||
            empty($codcomuna) ||
            empty($codbarrio) ||
            empty($codusuarioAuxiliar)
        ) {

            $_SESSION['error'] =
                "Todos los campos son obligatorios.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR FORMATO DE NOMBRE Y DIRECCIÓN (igual que al crear)
        // -----------------------------------------------------------
        if (mb_strlen($nombre) > 80) {

            $_SESSION['error'] =
                "El nombre no puede tener más de 80 caracteres.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }

        if (!preg_match('/^[\p{L} ]+$/u', $nombre)) {

            $_SESSION['error'] =
                "El nombre solo puede contener letras y espacios (sin números, símbolos ni puntuación).";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }

        if (!preg_match('/^(Calle|Carrera|Avenida)\b/iu', $direccion)) {

            $_SESSION['error'] =
                "La dirección debe iniciar con Calle, Carrera o Avenida.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }


        // VALIDAR AUXILIAR
        $sqlAuxiliar = "SELECT
                            u.codusuario

                        FROM tblusuario u

                        INNER JOIN tblrol r
                            ON r.codrol = u.codrol

                        WHERE u.codusuario = :codusuario
                        AND r.nombrerol = 'Auxiliar Zoocriadero'
                        AND u.estado = 'A'
                        AND r.estado = 'A'";

        $auxiliarExiste = $obj->select(
            $sqlAuxiliar,
            [
                ':codusuario' => $codusuarioAuxiliar
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$auxiliarExiste) {

            $_SESSION['error'] =
                "El auxiliar seleccionado no es válido.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }


        // ACTUALIZAR

        // AUDITORÍA: capturamos el valor anterior antes de sobreescribirlo
        $anterior = $obj->select(
            "SELECT nombrezoocriadero FROM tblzoocriadero WHERE codzoocriadero = :id",
            [':id' => $id]
        )->fetch(PDO::FETCH_ASSOC);

        $sql = "UPDATE tblzoocriadero

                SET
                    codusuario = :codusuario,
                    codbarrio = :codbarrio,
                    nombrezoocriadero = :nombre,
                    direccion = :direccion

                WHERE codzoocriadero = :id";


        $obj->update(
            $sql,
            [
                ':codusuario' => $codusuarioAuxiliar,
                ':codbarrio' => $codbarrio,
                ':nombre' => $nombre,
                ':direccion' => $direccion,
                ':id' => $id
            ]
        );


        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Zoocriadero',
            $id,
            $anterior['nombrezoocriadero'] ?? null,
            $nombre
        );


        $_SESSION['exito'] =
            "El zoocriadero se actualizó correctamente.";


        redirect(
            getUrl(
                'Zoocriadero',
                'Zoocriadero',
                'listZoo'
            )
        );

        exit();
    }


    // ---------------------------------------------------------------
    // HABILITAR / INHABILITAR
    // ---------------------------------------------------------------
    public function delete()
    {
        $obj = new Zoocriaderocor();

        $id =
            $_GET['id'] ?? null;


        $actual = $obj->select(
            "SELECT estado
             FROM tblzoocriadero
             WHERE codzoocriadero = :id",
            [
                ':id' => $id
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$actual) {

            return;
        }


        $nuevoEstado =
            ($actual['estado'] === 'A')
                ? 'I'
                : 'A';


        $obj->update(
            "UPDATE tblzoocriadero
             SET estado = :estado
             WHERE codzoocriadero = :id",
            [
                ':estado' => $nuevoEstado,
                ':id' => $id
            ]
        );


        $this->registrarBitacora(
            $obj,
            'UPDATE',
            'Zoocriadero',
            $id,
            $actual['estado'] === 'A' ? 'Activo' : 'Inactivo',
            $nuevoEstado === 'A' ? 'Activo' : 'Inactivo'
        );


        redirect(
            getUrl(
                'Zoocriadero',
                'Zoocriadero',
                'listZoo'
            )
        );

        exit();
    }


    // ---------------------------------------------------------------
    // FILTRO
    // ---------------------------------------------------------------
    public function filtro()
    {
        $obj = new Zoocriaderocor();

        $buscar =
            $_GET['buscar'] ?? '';


        $sql = "SELECT
                    z.codzoocriadero AS id,
                    z.nombrezoocriadero AS nombre,
                    z.direccion,
                    c.nombrecomuna AS comuna,
                    b.nombrebarrio AS barrio,
                    u.nombreusuario || ' ' || u.apellidousuario AS encargado,
                    z.estado

                FROM tblzoocriadero z

                INNER JOIN tblbarrio b
                    ON b.codbarrio = z.codbarrio

                INNER JOIN tblcomuna c
                    ON c.codcomuna = b.codcomuna

                INNER JOIN tblusuario u
                    ON u.codusuario = z.codusuario

                WHERE
                    z.nombrezoocriadero ILIKE :buscar
                    OR z.direccion ILIKE :buscar
                    OR c.nombrecomuna ILIKE :buscar
                    OR b.nombrebarrio ILIKE :buscar
                    OR u.nombreusuario ILIKE :buscar
                    OR u.apellidousuario ILIKE :buscar

                ORDER BY z.codzoocriadero ASC";


        $zoocriaderos = $obj->select(
            $sql,
            [
                ':buscar' => "%$buscar%"
            ]
        );


        include_once __DIR__
            . '/../../../view/Zoocriadero/filtroZoo.php';
    }
}