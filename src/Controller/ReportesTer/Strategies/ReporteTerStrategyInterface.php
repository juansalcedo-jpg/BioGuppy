<?php

namespace BioGuppy\Controller\ReportesTer\Strategies;

interface ReporteTerStrategyInterface
{
    public function generar($obj, $fechaDesde, $fechaHasta): array;
}
