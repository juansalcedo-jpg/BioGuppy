<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class ActividadStrategy implements ReporteTerStrategyInterface
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
                    s.nombresitio AS sitio,
                    u.nombreusuario || ' ' || u.apellidousuario AS responsable,
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
                ORDER BY a.fecha ASC, a.codactividad ASC";

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Por tipo de actividad',
            'columnas' => ['Fecha', 'Tipo', 'Sitio', 'Responsable', 'Estado'],
            'campos'   => ['fecha', 'tipo', 'sitio', 'responsable', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
