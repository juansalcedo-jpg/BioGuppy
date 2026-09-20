<?php

namespace BioGuppy\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use BioGuppy\Controller\ActividadesZoo\ActividadZooHelpersTrait;
use BioGuppy\Controller\ActividadesListZoo\ActividadesListZooController;

/**
 * Módulos de actividades del zoocriadero.
 *   UT-ActividadesZoo-001    ActividadZooHelpersTrait::obtenerCodTipoActividadZoo()
 *   UT-ActividadesZoo-002    ActividadZooHelpersTrait::obtenerTanquesActivos() y consultarSeguro()
 *   UT-ActividadesListZoo-001 ActividadesListZooController::sqlMisActividades()
 * No usan la base de datos: se entrega un "modelo falso" que guarda la consulta recibida.
 */
class ActividadesTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    /** consultarSeguro() escribe en error_log(): lo mandamos a un archivo temporal para no ensuciar la consola. */
    private function silenciarErrorLog(): void
    {
        ini_set('error_log', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bioguppy_phpunit.log');
    }

    /** Clase que usa el trait para poder llamar sus métodos privados. */
    private function ayudante()
    {
        return new class {
            use ActividadZooHelpersTrait;
            public function llamar(string $metodo, ...$args) { return $this->$metodo(...$args); }
        };
    }

    /** Modelo falso: guarda el SQL y los parámetros y devuelve un resultado fijo. */
    private function modeloFalso($fila, $lanzar = false)
    {
        return new class($fila, $lanzar) {
            public $sql = '';
            public $params = [];
            public function __construct(private $fila, private $lanzar) {}
            public function select($sql, $params = [])
            {
                if ($this->lanzar) {
                    throw new \RuntimeException('Fallo simulado de la base de datos');
                }
                $this->sql = $sql;
                $this->params = $params;
                $fila = $this->fila;
                return new class($fila) {
                    public function __construct(private $fila) {}
                    public function fetch($modo = null) { return $this->fila; }
                };
            }
        };
    }

    // UT-ActividadesZoo-001: devuelve el código del tipo de actividad, o null si no existe
    public function testObtenerCodTipoActividad()
    {
        $ayudante = $this->ayudante();

        $existe = $this->modeloFalso(['codtipoactividad' => 3]);
        $this->assertSame(3, $ayudante->llamar('obtenerCodTipoActividadZoo', $existe, 'ALIMENTACIÓN'));
        $this->assertSame([':nombre' => 'ALIMENTACIÓN'], $existe->params);

        $noExiste = $this->modeloFalso(false);
        $this->assertNull($ayudante->llamar('obtenerCodTipoActividadZoo', $noExiste, 'INEXISTENTE'));
    }

    
    public function testObtenerTanquesActivosFiltro()
    {
        $this->silenciarErrorLog();
        $ayudante = $this->ayudante();

        $modelo = $this->modeloFalso(['id' => 1]);
        $resultado = $ayudante->llamar('obtenerTanquesActivos', $modelo);
        $this->assertNotFalse($resultado);
        $this->assertStringContainsString("tk.estado = 'A'", $modelo->sql);
        $this->assertStringContainsString("z.estado = 'A'", $modelo->sql);

        $modeloConFallo = $this->modeloFalso(null, true);
        $this->assertFalse($ayudante->llamar('obtenerTanquesActivos', $modeloConFallo));
    }

    
    public function testSqlMisActividades()
    {
        $c = new ActividadesListZooController();
        $metodo = new ReflectionMethod($c, 'sqlMisActividades');

        $sinExtra = $metodo->invoke($c);
        $this->assertStringContainsString('WHERE a.codusuario = :codusuario', $sinExtra);
        $this->assertStringNotContainsString("a.estado = 'A'", $sinExtra);

        $conExtra = $metodo->invoke($c, "AND a.estado = 'A'");
        $this->assertStringContainsString('WHERE a.codusuario = :codusuario', $conExtra);
        $this->assertLessThan(
            strpos($conExtra, 'ORDER BY'),
            strpos($conExtra, "AND a.estado = 'A'"),
            'La condición extra debe ir antes del ORDER BY'
        );
    }
}
