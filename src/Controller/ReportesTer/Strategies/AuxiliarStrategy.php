<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class AuxiliarStrategy implements ReporteTerStrategyInterface
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
                    u.nombreusuario || ' ' || u.apellidousuario AS auxiliar,
                    s.nombresitio AS sitio,
                    ta.nombreactividad AS tipo,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno ta
                    ON ta.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s
                    ON s.codsitio = a.codsitio
                INNER JOIN tblusuario u
                    ON u.codusuario = a.codusuario
                WHERE a.fecha
                    BETWEEN :fechaDesde AND :fechaHasta
                ORDER BY u.nombreusuario ASC, a.fecha ASC";

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Por auxiliar',
            'columnas' => ['Fecha', 'Auxiliar', 'Sitio', 'Tipo', 'Estado'],
            'campos'   => ['fecha', 'auxiliar', 'sitio', 'tipo', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
