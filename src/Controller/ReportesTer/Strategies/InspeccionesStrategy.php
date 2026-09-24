<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class InspeccionesStrategy implements ReporteTerStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        // Solo actividades cuyo tipo sea "Inspección" (con o sin tilde)
        $sql = "SELECT
                    TO_CHAR(a.fecha, 'DD/MM/YYYY') AS fecha,
                    s.nombresitio AS sitio,
                    u.nombreusuario || ' ' || u.apellidousuario AS inspector,
                    COALESCE(NULLIF(TRIM(a.observaciones), ''), 'Sin observaciones') AS observaciones,
                    a.estado
                FROM tblactividadterreno a
                INNER JOIN tbltipoactividadterreno ta ON ta.codtipoactividad = a.codtipoactividad
                INNER JOIN tblsitio s ON s.codsitio = a.codsitio
                INNER JOIN tblusuario u ON u.codusuario = a.codusuario
                WHERE a.fecha::date BETWEEN :fechaDesde AND :fechaHasta
                  AND TRANSLATE(UPPER(ta.nombreactividad), 'ÁÉÍÓÚ', 'AEIOU') LIKE 'INSPECCION%'
                ORDER BY a.fecha ASC, a.codactividad ASC";

        $stmt = ($this->consultarSeguro)($obj, $sql, [
            ':fechaDesde' => $fechaDesde,
            ':fechaHasta' => $fechaHasta
        ]);

        return [
            'titulo'   => 'Inspecciones de terreno',
            'columnas' => ['Fecha', 'Sitio / Área', 'Inspector', 'Observaciones', 'Estado'],
            'campos'   => ['fecha', 'sitio', 'inspector', 'observaciones', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
