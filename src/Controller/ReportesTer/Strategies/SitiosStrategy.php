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
        $sql = "SELECT
                    s.fechacreacion::date AS fecha,
                    s.nombresitio AS sitio,
                    s.direccion AS ubicacion,
                    u.nombreusuario || ' ' || u.apellidousuario AS responsable,
                    s.estado
                FROM tblsitio s
                LEFT JOIN tblusuario u
                    ON u.codusuario = s.codusuario
                WHERE s.fechacreacion::date
                    BETWEEN :fechaDesde AND :fechaHasta
                ORDER BY s.fechacreacion ASC";

        $stmt = ($this->consultarSeguro)(
            $obj,
            $sql,
            [
                ':fechaDesde' => $fechaDesde,
                ':fechaHasta' => $fechaHasta
            ]
        );

        return [
            'titulo'   => 'Reporte de sitios',
            'columnas' => ['Fecha', 'Sitio', 'Ubicación', 'Responsable', 'Estado'],
            'campos'   => ['fecha', 'sitio', 'ubicacion', 'responsable', 'estado'],
            'datos'    => $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []
        ];
    }
}
