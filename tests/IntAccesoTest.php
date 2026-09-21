<?php

namespace BioGuppy\Tests;

use BioGuppy\Controller\Acceso\AccesoController;

require_once __DIR__ . '/BaseDB.php';


class IntAccesoTest extends BaseDB
{
    private function entrar(string $correo, string $clave): void
    {
        $_POST = ['usu_correo' => $correo, 'usu_clave' => $clave];
        ob_start();
        (new AccesoController($this->db))->login();
        ob_end_clean();
    }

    
    public function testLoginOk()
    {
        $this->usuario('Coordinador Control Biologico', 'juan.perez@ejemplo.com', '1098765001', '3001230001');

        $this->entrar('juan.perez@ejemplo.com', 'Prueba123*');

        $this->assertTrue(isset($_SESSION['auth']));
        $this->assertSame('Coordinador Control Biologico', $_SESSION['nombre_rol']);
        $this->assertSame('Zoocriadero', $_SESSION['modulo']);
    }

    
    public function testInactivo()
    {
        $this->usuario('Auxiliar Terreno', 'daniel.ceballos@ejemplo.com', '1098765002', '3001230002', 'I', 'Luis', 'Gómez');

        $this->entrar('daniel.ceballos@ejemplo.com', 'Prueba123*');

        $this->assertFalse(isset($_SESSION['auth']));
        $this->assertSame('La cuenta esta inactiva', $_SESSION['ErrorLogin']);
    }

    
    public function testUsuario()
    {
        $this->usuario('Auxiliar Zoocriadero', 'laura.martinez@ejemplo.com', '1098765432', '3001234567', 'A', 'Laura', 'Martínez');

        $fila = $this->uno(
            'SELECT u.nombreusuario, u.apellidousuario, u.estado, r.nombrerol
             FROM tblusuario u JOIN tblrol r ON r.codrol = u.codrol
             WHERE u.correo = :c',
            [':c' => 'laura.martinez@ejemplo.com']
        );

        $this->assertSame('Laura', $fila['nombreusuario']);
        $this->assertSame('Martínez', $fila['apellidousuario']);
        $this->assertSame('Auxiliar Zoocriadero', $fila['nombrerol']);
        $this->assertSame('A', $fila['estado']);
    }
}
