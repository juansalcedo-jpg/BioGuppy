<?php

namespace BioGuppy\Tests;

use PHPUnit\Framework\TestCase;
use BioGuppy\Controller\Acceso\AccesoController;
use BioGuppy\Controller\CambioContra\CambioContraController;
use BioGuppy\Model\Acceso\AccesoModel;

/**
 * Pruebas de acceso y seguridad:
 *   UT-Login-002        AccesoController::login()  con correo no registrado
 *   UT-Permisos-001     usuarioTienePermiso()      separación de módulos por rol
 *   UT-Recuperacion-001 CambioContraController::enviarCorreo() validación del correo
 * (UT-Login-001, login válido, ya existe en AccesoControllerTest.php)
 */
class SeguridadTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    /** Simula lo que devuelve PDOStatement: rowCount() y fetch(). */
    private function resultado(int $filas, $fila = false)
    {
        return new class($filas, $fila) {
            public function __construct(private int $filas, private $fila) {}
            public function rowCount() { return $this->filas; }
            public function fetch($modo = null) { return $this->fila; }
        };
    }

    
    public function testLoginConCorreoNoRegistrado()
    {
        $_POST['usu_correo'] = 'noexiste@ejemplo.com';
        $_POST['usu_clave']  = 'Prueba123*';

        
        $modelo = $this->createStub(AccesoModel::class);
        $modelo->method('select')->willReturn($this->resultado(0, false));

        
        $avisos = [];
        set_error_handler(function ($nivel, $mensaje) use (&$avisos) {
            $avisos[] = $mensaje;
            return true;
        });

        ob_start();
        try {
            (new AccesoController($modelo))->login();
        } finally {
            $salida = ob_get_clean();
            restore_error_handler();
        }

        $this->assertSame('Correo o contraseña incorrectos', $_SESSION['ErrorLogin']);
        $this->assertArrayNotHasKey('auth', $_SESSION);
        $this->assertStringContainsString('inicio/login.php', $salida);
        $this->assertSame([], $avisos, 'login() lanzó avisos de PHP: ' . implode(' | ', $avisos));
    }

    
    public function testRolModulos()
    {
        $_SESSION['nombre_rol'] = 'Administrador';
        $this->assertTrue(usuarioTienePermiso('Catalogos', 'TipoTanque', 'listTipoTanque'));
        $this->assertFalse(usuarioTienePermiso('Usuarios', 'Usuarios', 'listUsu'));

        
        $_SESSION['nombre_rol'] = 'Visitante';
        $this->assertFalse(usuarioTienePermiso('Tanques', 'Tanques', 'listTan'));
    }

    
    public function testEnviarCorreoRechaza()
    {
        $controlador = new CambioContraController();

        $_POST['correo'] = '';
        ob_start();
        $controlador->enviarCorreo();
        $salidaVacio = ob_get_clean();
        $this->assertStringContainsString('Debes ingresar un correo electrónico.', $salidaVacio);

        $_POST['correo'] = 'usuario.sin.arroba';
        ob_start();
        $controlador->enviarCorreo();
        $salidaInvalido = ob_get_clean();
        $this->assertStringContainsString('El correo ingresado no es válido.', $salidaInvalido);
    }
}
