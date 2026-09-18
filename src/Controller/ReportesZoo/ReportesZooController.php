<?php

namespace BioGuppy\Controller\ReportesZoo;

use BioGuppy\Model\ReporteZoo\ReporteZoo;
use PDO;

class ReportesZooController
{

    // ---------------------------------------------------------------
    // VISTA PRINCIPAL
    // ---------------------------------------------------------------
    public function listRepoZoo()
    {
        include_once __DIR__
            . '/../../../view/ReportesZoo/listRepoZoo.php';
    }


    // ---------------------------------------------------------------
    // GENERAR REPORTE
    // ---------------------------------------------------------------
    public function generar()
    {
        $obj = new ReporteZoo();


        // -----------------------------------------------------------
        // RECIBIR DATOS
        // -----------------------------------------------------------
        $tipoReporte =
            trim($_POST['tipoReporte'] ?? '');

        $fechaDesde =
            trim($_POST['fechaDesde'] ?? '');

        $fechaHasta =
            trim($_POST['fechaHasta'] ?? '');


        // -----------------------------------------------------------
        // VALIDAR CAMPOS VACÍOS
        // -----------------------------------------------------------
        if (
            empty($tipoReporte) ||
            empty($fechaDesde) ||
            empty($fechaHasta)
        ) {

            echo json_encode(
                [
                    'ok' => false,
                    'mensaje' =>
                        'Debe seleccionar el tipo de reporte y las fechas.'
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // -----------------------------------------------------------
        // EXPRESIÓN REGULAR PARA FECHAS
        // FORMATO AAAA-MM-DD
        // -----------------------------------------------------------
        $regexFecha =
            '/^\d{4}-\d{2}-\d{2}$/';


        if (
            !preg_match(
                $regexFecha,
                $fechaDesde
            ) ||
            !preg_match(
                $regexFecha,
                $fechaHasta
            )
        ) {

            echo json_encode(
                [
                    'ok' => false,
                    'mensaje' =>
                        'Las fechas ingresadas no son válidas.'
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR TIPO DE REPORTE
        // -----------------------------------------------------------
        $regexTipo =
            '/^(seguimiento|mortalidad|tanques)$/';


        if (
            !preg_match(
                $regexTipo,
                $tipoReporte
            )
        ) {

            echo json_encode(
                [
                    'ok' => false,
                    'mensaje' =>
                        'El tipo de reporte no es válido.'
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // -----------------------------------------------------------
        // VALIDAR RANGO DE FECHAS
        // -----------------------------------------------------------
        if ($fechaDesde > $fechaHasta) {

            echo json_encode(
                [
                    'ok' => false,
                    'mensaje' =>
                        'La fecha desde no puede ser mayor que la fecha hasta.'
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // ===========================================================
        // 1. SEGUIMIENTO DE ACTIVIDADES
        // ===========================================================
        if ($tipoReporte === 'seguimiento') {

            $sql = "SELECT
                        a.fecha,

                        ta.nombreactividad AS tipo,

                        'Tanque ' ||
                        t.numerotanque AS tanque,

                        u.nombreusuario || ' ' ||
                        u.apellidousuario AS responsable,

                        COALESCE(
                            a.observaciones,
                            'Sin observaciones'
                        ) AS observaciones,

                        a.estado

                    FROM tblactividadzoo a

                    INNER JOIN tbltipoactividadzoo ta
                        ON ta.codtipoactividad =
                           a.codtipoactividad

                    INNER JOIN tblzootanque t
                        ON t.codtanque =
                           a.codtanque

                    INNER JOIN tblusuario u
                        ON u.codusuario =
                           a.codusuario

                    WHERE a.fecha
                        BETWEEN :fechaDesde
                        AND :fechaHasta

                    ORDER BY
                        a.fecha ASC,
                        a.codactividad ASC";


            $stmt = $obj->select(
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );


            $datos =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            echo json_encode(
                [
                    'ok' => true,
                    'tipo' => 'seguimiento',
                    'titulo' =>
                        'Seguimiento de actividades',
                    'datos' => $datos
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // ===========================================================
        // 2. NACIDOS Y MUERTOS POR TANQUE
        // ===========================================================
        if ($tipoReporte === 'mortalidad') {

            $sql = "SELECT
                        a.fecha,

                        'Tanque ' ||
                        t.numerotanque AS tanque,

                        z.nombrezoocriadero
                            AS zoocriadero,

                        COALESCE(
                            a.pecesnacidos,
                            0
                        ) AS nacidos,

                        COALESCE(
                            a.pecesmuertos,
                            0
                        ) AS muertos,

                        u.nombreusuario || ' ' ||
                        u.apellidousuario
                            AS responsable,

                        a.estado

                    FROM tblactividadzoo a

                    INNER JOIN tbltipoactividadzoo ta
                        ON ta.codtipoactividad =
                           a.codtipoactividad

                    INNER JOIN tblzootanque t
                        ON t.codtanque =
                           a.codtanque

                    INNER JOIN tblzoocriadero z
                        ON z.codzoocriadero =
                           t.codzoocriadero

                    INNER JOIN tblusuario u
                        ON u.codusuario =
                           a.codusuario

                    WHERE a.fecha
                        BETWEEN :fechaDesde
                        AND :fechaHasta

                    AND ta.nombreactividad =
                        'RECOLECCIÓN'

                    ORDER BY
                        a.fecha ASC,
                        a.codactividad ASC";


            $stmt = $obj->select(
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );


            $datos =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            echo json_encode(
                [
                    'ok' => true,
                    'tipo' => 'mortalidad',
                    'titulo' =>
                        'Nacidos y muertos por tanque',
                    'datos' => $datos
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }


        // ===========================================================
        // 3. TANQUES POR ZOOCRIADERO
        // ===========================================================
        if ($tipoReporte === 'tanques') {

            $sql = "SELECT
                        t.fechacreacion::date
                            AS fecha,

                        z.nombrezoocriadero
                            AS zoocriadero,

                        'Tanque ' ||
                        t.numerotanque
                            AS tanque,

                        tt.nombretipotanque
                            AS tipo,

                        t.capacidad,

                        t.estado

                    FROM tblzootanque t

                    INNER JOIN tblzoocriadero z
                        ON z.codzoocriadero =
                           t.codzoocriadero

                    INNER JOIN tbltipotanque tt
                        ON tt.codtipotanque =
                           t.codtipotanque

                    WHERE t.fechacreacion::date
                        BETWEEN :fechaDesde
                        AND :fechaHasta

                    ORDER BY
                        z.nombrezoocriadero ASC,
                        t.numerotanque ASC";


            $stmt = $obj->select(
                $sql,
                [
                    ':fechaDesde' => $fechaDesde,
                    ':fechaHasta' => $fechaHasta
                ]
            );


            $datos =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            echo json_encode(
                [
                    'ok' => true,
                    'tipo' => 'tanques',
                    'titulo' =>
                        'Tanques por zoocriadero',
                    'datos' => $datos
                ],
                JSON_UNESCAPED_UNICODE
            );

            exit();
        }
    }
}