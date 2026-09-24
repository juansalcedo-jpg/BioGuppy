<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

use PDO;

class SitiosStrategy implements ReporteTerStrategyInterface
{
    private $consultarSeguro;

    public function __construct(callable $consultarSeguro)
    {
        $this->consultarSeguro = $consultarSeguro;
    }

    public function generar($obj, $fechaDesde, $fechaHasta): array
    {
        // Sitios registrados en el rango o que tuvieron actividades en el rango
        $sql = "SELECT
                    TO_CHAR(s.fechacreacion, 'DD/MM/YYYY') AS fecha,
                    s.nombresitio AS nombre_sitio,
                    s.direccion || ' — ' || b.nombrebarrio || ', ' || c.nombrecomuna AS ubicacion,
                    td.nombretipodeposito AS tipodeposito,
                    COUNT(a.codactividad) AS actividades,
                    s.estado
                FROM tblsitio s
                INNER JOIN tblcomuna c ON c.codcomuna = s.codcomuna
                INNER JOIN tblbarrio b ON b.codbarrio = s.codbarrio
                INNER JOIN tbltipodeposito td ON td.codtipodeposito = s.codtipodeposito
                LEFT JOIN tblactividadterreno a
                    ON a.codsitio = s.codsitio
                   AND a.fecha::date BETWEEN :fechaDesde AND :fechaHasta
                GROUP BY s.codsitio, s.fechacreacion, s.nombresitio, s.direccion,
                         b.nombrebarrio, c.nombrecomuna, td.nombretipodeposito, s.estado
                HAVING s.fechacreacion::date BETWEEN :fechaDesde2 AND :fechaHasta2
                    OR COUNT(a.codactividad) > 0
                ORDER BY s.nombresitio ASC";

        $stmt = ($this->consultarSeguro)($obj, $sql, [
            ':fechaDesde'  => $fechaDesde,
            ':fechaHasta'  => $fechaHasta,
            ':fechaDesde2' => $fechaDesde,
            ':fechaHasta2' => $fechaHasta
        ]);

        return [
            'titulo'   => 'Estado de sitios y áreas',
            'columnas' => ['Fecha registro', 'Nombre del sitio', 'Ubicación', 'Tipo de depósito', 'Actividades', 'Estado'],
            'campos'   => ['fecha', 'nombre_sitio', 'ubicacion', 'tipodeposito', 'actividades', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
