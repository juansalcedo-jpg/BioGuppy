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
        // Simular POST
        $_POST['usu_correo'] = 'correo@ejemplo.com';
        $_POST['usu_clave'] = 'claveCorrecta';

        // Simular el modelo con un mock: el ->select() de login() se llama dos
        // veces (usuario y luego rol), y como devolvemos siempre la misma fila,
        // usamos rowCount()=1 y un fetch() que acepta el argumento PDO::FETCH_ASSOC
        // tal como lo llama el controlador.
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

    public function testLoginConCredencialesIncorrectas() {
        $_POST['usu_correo'] = 'correo@ejemplo.com';
        $_POST['usu_clave'] = 'claveIncorrecta';

        $mockModel = $this->createMock(AccesoModel::class);
        $mockModel->method('select')
                  ->willReturn(new class {
                      public function rowCount() { return 1; }
                      public function fetch($mode = null) {
                          return [
                              'contrasena'    => password_hash('otraClave', PASSWORD_DEFAULT),
                              'nombreusuario' => 'Juan',
                              'correo'        => 'correo@ejemplo.com',
                              'codusuario'    => 1,
                          ];
                      }
                  });

        $controller = new AccesoController($mockModel);
        $controller->login();

        $this->assertEquals('Correo o contraseña incorrectos', $_SESSION['ErrorLogin']);
        $this->assertArrayNotHasKey('auth', $_SESSION);
    }
}
