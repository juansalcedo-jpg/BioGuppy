<?php

namespace BioGuppy\Tests;

use PHPUnit\Framework\TestCase;
use BioGuppy\Controller\Acceso\AccesoController;
use BioGuppy\Model\Acceso\AccesoModel;

class AccesoControllerTest extends TestCase {

    protected function setUp(): void {
        $_SESSION = [];
        $_POST = [];
    }

    public function testLoginConUsuarioValido() {
        $_POST['usu_correo'] = 'correo@ejemplo.com';
        $_POST['usu_clave'] = 'claveCorrecta';

        $mockModel = $this->createMock(AccesoModel::class);
        $mockModel->method('select')
                  ->willReturn(new class {
                      public function rowCount() { return 1; }
                      public function fetch($mode = null) {
                          return [
                              'contrasena'    => password_hash('claveCorrecta', PASSWORD_DEFAULT),
                              'nombreusuario' => 'Juan',
                              'correo'        => 'correo@ejemplo.com',
                              'codusuario'    => 1,
                              'nombrerol'     => 'Super Admin',
                          ];
                      }
                  });

        // Inyectar el mock en el controlador via el constructor
        $controller = new AccesoController($mockModel);

        // Ejecutar login (redirect() hace un echo de <script>, no corta la ejecucion)
        $controller->login();

        // Verificar que la sesión se llenó
        $this->assertEquals('Juan', $_SESSION['usu_nombre']);
        $this->assertEquals('correo@ejemplo.com', $_SESSION['usu_correo']);
        $this->assertEquals('ok', $_SESSION['auth']);
    }

    
}
