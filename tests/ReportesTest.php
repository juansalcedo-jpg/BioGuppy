<?php

namespace BioGuppy\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use BioGuppy\Controller\ReportesZoo\ReportesZooController;
use BioGuppy\Controller\ReportesTer\ReportesTerController;

/**
 * Módulos de reportes (Zoocriadero y Terreno).
 *   UT-ReportesZoo-001  ReportesZooController::validarParametros()
 *   UT-ReportesTer-001  ReportesTerController::validarParametros()
 * validarParametros() es privado: se invoca con Reflection, sin modificar el código.
 */
class ReportesTest extends TestCase
{
    /** Llama a validarParametros() y devuelve [resultado, mensajeError]. */
    private function validar($controlador, $tipo, $desde, $hasta): array
    {
        $mensaje = '';
        $metodo = new ReflectionMethod($controlador, 'validarParametros');
        $args = [$tipo, $desde, $hasta, &$mensaje]; // el 4.º parámetro va por referencia
        $ok = $metodo->invokeArgs($controlador, $args);
        return [$ok, $mensaje];
    }

    
    public function testReporteZooTipoFechas()
    {
        $c = new ReportesZooController();

        [$ok, $msg] = $this->validar($c, 'mortalidad', '2026-01-01', '2026-01-31');
        $this->assertTrue($ok);
        $this->assertSame('', $msg);

        [$ok, $msg] = $this->validar($c, 'ventas', '2026-01-01', '2026-01-31');
        $this->assertFalse($ok);
        $this->assertSame('El tipo de reporte no es válido.', $msg);

        [$ok, $msg] = $this->validar($c, '', '2026-01-01', '2026-01-31');
        $this->assertFalse($ok);
        $this->assertSame('Debe seleccionar el tipo de reporte y las fechas.', $msg);

        [$ok, $msg] = $this->validar($c, 'tanques', '01/01/2026', '2026-01-31');
        $this->assertFalse($ok);
        $this->assertSame('Las fechas ingresadas no son válidas.', $msg);
    }

    // UT-ReportesTer-001: el reporte de terreno rechaza el rango invertido y los tipos de zoocriadero
    public function testReporteTerreRechazaRangoYTipo()
    {
        $c = new ReportesTerController();

        [$ok, $msg] = $this->validar($c, 'sitios', '2026-02-01', '2026-01-01');
        $this->assertFalse($ok);
        $this->assertSame('La fecha desde no puede ser mayor que la fecha hasta.', $msg);

        
        [$ok, $msg] = $this->validar($c, 'mortalidad', '2026-01-01', '2026-01-31');
        $this->assertFalse($ok);
        $this->assertSame('El tipo de reporte no es válido.', $msg);

        [$ok, $msg] = $this->validar($c, 'deposito', '2026-01-01', '2026-01-31');
        $this->assertTrue($ok);
    }
}
