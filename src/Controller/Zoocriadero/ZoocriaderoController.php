<?php

namespace BioGuppy\Controller\Zoocriadero;

use BioGuppy\Model\ZoocriaderoCor\Zoocriaderocor;
use PDO;

class ZoocriaderoController
{

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
                    z.auxencargado AS encargado,
                    z.estado
                FROM tblzoocriadero z

                INNER JOIN tblbarrio b
                    ON b.codbarrio = z.codbarrio

                INNER JOIN tblcomuna c
                    ON c.codcomuna = b.codcomuna

                ORDER BY z.codzoocriadero ASC";

        $zoocriaderos = $obj->select($sql);

        include_once __DIR__
            . '/../../../view/Zoocriadero/listZoo.php';
    }


    // ---------------------------------------------------------------
    // FORMULARIO PARA REGISTRAR ZOOCRIADERO
    // ---------------------------------------------------------------
    public function create()
    {
        $obj = new Zoocriaderocor();


        // COMUNAS ACTIVAS
        $sqlComunas = "SELECT
                            codcomuna,
                            nombrecomuna
                       FROM tblcomuna
                       WHERE estado = 'A'
                       ORDER BY nombrecomuna ASC";

        $comunas = $obj->select($sqlComunas);


        // BARRIOS ACTIVOS
        $sqlBarrios = "SELECT
                            codbarrio,
                            codcomuna,
                            nombrebarrio
                       FROM tblbarrio
                       WHERE estado = 'A'
                       ORDER BY nombrebarrio ASC";

        $barrios = $obj->select($sqlBarrios);


        include_once __DIR__
            . '/../../../view/Zoocriadero/create.php';
    }


    // ---------------------------------------------------------------
    // REGISTRAR ZOOCRIADERO
    // ---------------------------------------------------------------
    public function postCreateZoo()
    {
        $obj = new Zoocriaderocor();


        // DATOS DEL FORMULARIO
        $nombre =
            trim($_POST['nombre'] ?? '');

        $direccion =
            trim($_POST['direccion'] ?? '');

        $codcomuna =
            $_POST['codcomuna'] ?? '';

        $codbarrio =
            $_POST['codbarrio'] ?? '';

        $auxencargado =
            trim($_POST['encargado'] ?? '');

        $estado =
            isset($_POST['estado']) ? 'A' : 'I';


        // USUARIO QUE ESTÁ REGISTRANDO
        $codusuario =
            $_SESSION['usu_id'] ?? null;


        // VALIDAR SESIÓN
        if (empty($codusuario)) {

            $_SESSION['error'] =
                "Tu sesión expiró, vuelve a iniciar sesión.";

            redirect("inicio/login.php");

            exit();
        }


        // VALIDAR CAMPOS
        if (
            empty($nombre) ||
            empty($direccion) ||
            empty($codcomuna) ||
            empty($codbarrio) ||
            empty($auxencargado)
        ) {

            $_SESSION['error'] =
                "Nombre, dirección, comuna, barrio y encargado son obligatorios.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'create'
                )
            );

            exit();
        }


        // VALIDAR COMUNA
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


        // VALIDAR BARRIO Y COMUNA
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


        // REGISTRAR
        $sql = "INSERT INTO tblzoocriadero
                (
                    codusuario,
                    codbarrio,
                    nombrezoocriadero,
                    direccion,
                    auxencargado,
                    fechacreacion,
                    estado
                )
                VALUES
                (
                    :codusuario,
                    :codbarrio,
                    :nombre,
                    :direccion,
                    :auxencargado,
                    DEFAULT,
                    :estado
                )";


        $obj->insert(
            $sql,
            [
                ':codusuario' => $codusuario,
                ':codbarrio' => $codbarrio,
                ':nombre' => $nombre,
                ':direccion' => $direccion,
                ':auxencargado' => $auxencargado,
                ':estado' => $estado
            ]
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
    // FORMULARIO DE EDICIÓN
    // ---------------------------------------------------------------
    public function getUpdate()
    {
        $obj = new Zoocriaderocor();

        $id =
            $_GET['id'] ?? null;


        // VALIDAR ID
        if (empty($id)) {

            $_SESSION['error'] =
                "Zoocriadero no válido.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
        }


        // TRAER ZOOCRIADERO
        $sqlZoo = "SELECT
                        z.codzoocriadero,
                        z.nombrezoocriadero,
                        z.direccion,
                        z.codbarrio,
                        z.auxencargado,
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


        // VALIDAR QUE EXISTA
        if (!$zoocriadero) {

            $_SESSION['error'] =
                "El zoocriadero no existe.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
        }


        // COMUNAS ACTIVAS
        $sqlComunas = "SELECT
                            codcomuna,
                            nombrecomuna
                       FROM tblcomuna
                       WHERE estado = 'A'
                       ORDER BY nombrecomuna ASC";

        $comunas =
            $obj->select($sqlComunas);


        // BARRIOS ACTIVOS
        $sqlBarrios = "SELECT
                            codbarrio,
                            codcomuna,
                            nombrebarrio
                       FROM tblbarrio
                       WHERE estado = 'A'
                       ORDER BY nombrebarrio ASC";

        $barrios =
            $obj->select($sqlBarrios);


        // CARGAR FORMULARIO
        include_once __DIR__
            . '/../../../view/Zoocriadero/Getupdatezoo.php';
    }


    // ---------------------------------------------------------------
    // GUARDAR EDICIÓN
    // ---------------------------------------------------------------
    public function postUpdateZoo()
    {
        $obj = new Zoocriaderocor();


        // DATOS DEL FORMULARIO
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

        $auxencargado =
            trim($_POST['encargado'] ?? '');

        $estado =
            isset($_POST['estado']) ? 'A' : 'I';


        // VALIDAR ID
        if (empty($id)) {

            $_SESSION['error'] =
                "Zoocriadero no válido.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
        }


        // VALIDAR CAMPOS
        if (
            empty($nombre) ||
            empty($direccion) ||
            empty($codcomuna) ||
            empty($codbarrio) ||
            empty($auxencargado)
        ) {

            $_SESSION['error'] =
                "Nombre, dirección, comuna, barrio y encargado son obligatorios.";

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


        // VALIDAR COMUNA
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
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }


        // VALIDAR BARRIO Y COMUNA
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
                    'getUpdate',
                    [
                        'id' => $id
                    ]
                )
            );

            exit();
        }


        // VALIDAR NOMBRE REPETIDO
        $sqlValidar = "SELECT codzoocriadero
                       FROM tblzoocriadero
                       WHERE nombrezoocriadero ILIKE :nombre
                       AND codzoocriadero != :id";

        $existe = $obj->select(
            $sqlValidar,
            [
                ':nombre' => $nombre,
                ':id' => $id
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if ($existe) {

            $_SESSION['error'] =
                "Ya existe otro zoocriadero con ese nombre.";

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
        $sql = "UPDATE tblzoocriadero
                SET
                    nombrezoocriadero = :nombre,
                    direccion = :direccion,
                    codbarrio = :codbarrio,
                    auxencargado = :auxencargado,
                    estado = :estado
                WHERE codzoocriadero = :id";


        $obj->update(
            $sql,
            [
                ':nombre' => $nombre,
                ':direccion' => $direccion,
                ':codbarrio' => $codbarrio,
                ':auxencargado' => $auxencargado,
                ':estado' => $estado,
                ':id' => $id
            ]
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
    // INHABILITAR / HABILITAR
    // ---------------------------------------------------------------
    public function delete()
    {
        $obj = new Zoocriaderocor();

        $id =
            $_GET['id'] ?? null;


        if (empty($id)) {

            $_SESSION['error'] =
                "Zoocriadero no válido.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
        }


        $actual = $obj->select(
            "SELECT estado
             FROM tblzoocriadero
             WHERE codzoocriadero = :id",
            [
                ':id' => $id
            ]
        )->fetch(PDO::FETCH_ASSOC);


        if (!$actual) {

            $_SESSION['error'] =
                "El zoocriadero no existe.";

            redirect(
                getUrl(
                    'Zoocriadero',
                    'Zoocriadero',
                    'listZoo'
                )
            );

            exit();
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


        $_SESSION['exito'] =
            "El estado del zoocriadero se actualizó correctamente.";


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
    // BUSCADOR AJAX
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
                    z.auxencargado AS encargado,
                    z.estado

                FROM tblzoocriadero z

                INNER JOIN tblbarrio b
                    ON b.codbarrio = z.codbarrio

                INNER JOIN tblcomuna c
                    ON c.codcomuna = b.codcomuna

                WHERE
                    z.nombrezoocriadero ILIKE :buscar
                    OR z.direccion ILIKE :buscar
                    OR c.nombrecomuna ILIKE :buscar
                    OR b.nombrebarrio ILIKE :buscar
                    OR z.auxencargado ILIKE :buscar

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