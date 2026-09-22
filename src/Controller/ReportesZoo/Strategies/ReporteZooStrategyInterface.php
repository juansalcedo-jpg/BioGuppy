<?php

namespace BioGuppy\Controller\ReportesZoo\Strategies;

interface ReporteZooStrategyInterface
{
    public function generar($obj, $fechaDesde, $fechaHasta): array;
}
