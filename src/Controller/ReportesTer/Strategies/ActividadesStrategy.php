<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class ActividadesStrategy implements ReporteTerStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        $sql = "SELECT
                    TO_CHAR(a.fecha, 'DD/MM/YYYY') AS fecha,
                    ta.nombreactividad AS actividad,
                    s.nombresitio AS sitio,
                    u.nombreusuario || ' ' || u.apellidousuario AS responsable,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno ta ON ta.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s ON s.codsitio = a.codsitio
                INNER JOIN tblusuario u ON u.codusuario = a.codusuario
                WHERE a.fecha::date BETWEEN :fechaDesde AND :fechaHasta
                ORDER BY a.fecha ASC, a.codactividad ASC";

        $stmt = ($this->consultarSeguro)($obj, $sql, [
            ':fechaDesde' => $fechaDesde,
            ':fechaHasta' => $fechaHasta
        ]);

        return [
            'titulo'   => 'Actividades de campo',
            'columnas' => ['Fecha', 'Actividad', 'Sitio', 'Responsable', 'Estado'],
            'campos'   => ['fecha', 'actividad', 'sitio', 'responsable', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
