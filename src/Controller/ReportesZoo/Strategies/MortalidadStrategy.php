<?php

namespace BioGuppy\Controller\ReportesZoo\Strategies;

use PDO;

class MortalidadStrategy implements ReporteZooStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        //Nacidos y muertos por tanque
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

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Nacidos y muertos por tanque',
            'columnas' => ['Fecha', 'Tanque', 'Zoocriadero', 'Nacidos', 'Muertos', 'Responsable', 'Estado'],
            'campos'   => ['fecha', 'tanque', 'zoocriadero', 'nacidos', 'muertos', 'responsable', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
