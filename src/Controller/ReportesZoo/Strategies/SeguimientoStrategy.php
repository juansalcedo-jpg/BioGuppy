<?php

namespace BioGuppy\Controller\ReportesZoo\Strategies;

use PDO;

class SeguimientoStrategy implements ReporteZooStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
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

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Seguimiento de actividades',
            'columnas' => ['Fecha', 'Tipo', 'Tanque', 'Responsable', 'Observaciones', 'Estado'],
            'campos'   => ['fecha', 'tipo', 'tanque', 'responsable', 'observaciones', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
