<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class DepositoStrategy implements ReporteTerStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        $sql = "SELECT
                    s.fechacreacion::date AS fecha,
                    s.nombresitio AS sitio,
                    td.nombretipodeposito AS tipo,
                    COUNT(a.codactividad) AS cantidad,
                    s.estado
                FROM tblsitio s
                INNER JOIN tbltipodeposito td
                    ON td.codtipodeposito = s.codtipodeposito
                LEFT JOIN tblactividadterreno a
                    ON a.codsitio = s.codsitio
                    AND a.fecha BETWEEN :fechaDesde AND :fechaHasta
                WHERE s.fechacreacion::date
                    BETWEEN :fechaDesde2 AND :fechaHasta2
                GROUP BY s.fechacreacion, s.nombresitio, td.nombretipodeposito, s.estado
                ORDER BY td.nombretipodeposito ASC";

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde'  => $fechaDesde,
                ':fechaHasta'  => $fechaHasta,
                ':fechaDesde2' => $fechaDesde,
                ':fechaHasta2' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Por tipo de depósito',
            'columnas' => ['Fecha', 'Sitio', 'Tipo de depósito', 'Cantidad', 'Estado'],
            'campos'   => ['fecha', 'sitio', 'tipo', 'cantidad', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
