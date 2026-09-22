<?php

namespace BioGuppy\Controller\ReportesZoo\Strategies;

use PDO;

class TanquesStrategy implements ReporteZooStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        //Tanques por Zoocriadero
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

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Tanques por zoocriadero',
            'columnas' => ['Fecha', 'Zoocriadero', 'Tanque', 'Tipo', 'Capacidad', 'Estado'],
            'campos'   => ['fecha', 'zoocriadero', 'tanque', 'tipo', 'capacidad', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
