<?php

namespace BioGuppy\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use BioGuppy\Controller\Depositos\DepositosController;

/**
 * Módulo Depósitos.
 *   UT-Depositos-001  DepositosController::consultarSeguro()
 * Cuando la base de datos falla, el usuario no debe ver el mensaje técnico.
 */
class DepositosTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    
    public function testConsultarSeguroNoExpone()
    {
        
        ini_set('error_log', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bioguppy_phpunit.log');

        $modeloConFallo = new class {
            public function select($sql, $params = [])
            {
                throw new \RuntimeException('SQLSTATE[42P01]: relation "tbltipodeposito" does not exist');
            }
        };

        $c = new DepositosController();
        $metodo = new ReflectionMethod($c, 'consultarSeguro');

        $resultado = $metodo->invoke($c, $modeloConFallo, 'SELECT 1');

        $this->assertFalse($resultado);
        $mensajeAlUsuario = $_SESSION['error'] ?? '';
        $this->assertStringNotContainsString('SQLSTATE', $mensajeAlUsuario);
        $this->assertStringNotContainsString('DEBUG', $mensajeAlUsuario);
    }
}
