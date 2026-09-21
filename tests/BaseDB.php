<?php

namespace BioGuppy\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use BioGuppy\Model\Acceso\AccesoModel;

abstract class BaseDB extends TestCase
{
    protected $db;   // un solo modelo = una sola conexión para toda la prueba

    protected function setUp(): void
    {
        if (!extension_loaded('pdo_pgsql')) {
            $this->fail('Activa extension=pdo_pgsql y extension=pgsql en C:\php\php.ini (php -m debe mostrar pdo_pgsql).');
        }
        $_SESSION = [];
        $_POST = [];
        $this->db = new AccesoModel();
        $this->db->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $con = $this->db->getConnection();
        if ($con->inTransaction()) {
            $con->rollBack();
        }
    }

    /
    protected function uno(string $sql, array $p = [])
    {
        return $this->db->select($sql, $p)->fetch(PDO::FETCH_ASSOC);
    }

    
    protected function cod(string $sql, array $p = []): int
    {
        $f = $this->uno($sql, $p);
        if (!$f) {
            $this->fail('Falta un dato de catálogo en la BD para: ' . $sql);
        }
        return (int) array_values($f)[0];
    }

    
    protected function usuario(string $rol, string $correo, string $doc, string $tel, string $estado = 'A', string $nombre = 'Ana', string $apellido = 'Pérez', string $clave = 'Prueba123*'): int
    {
        $existe = $this->uno(
            'SELECT 1 FROM tblusuario WHERE correo = :c OR numerodocumento = :d OR usutelefono = :t',
            [':c' => $correo, ':d' => $doc, ':t' => $tel]
        );
        if ($existe) {
            $this->markTestSkipped("Ya existe en la BD un usuario con el correo, documento o celular de prueba ($correo).");
        }

        $this->db->insert(
            'INSERT INTO public.tblusuario VALUES (DEFAULT, :rol, 1, :doc, :nombre, :apellido, :tel, :correo, :clave, DEFAULT, :estado)',
            [
                ':rol'      => $this->cod('SELECT codrol FROM tblrol WHERE nombrerol = :n', [':n' => $rol]),
                ':doc'      => $doc,
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':tel'      => $tel,
                ':correo'   => $correo,
                ':clave'    => password_hash($clave, PASSWORD_DEFAULT),
                ':estado'   => $estado,
            ]
        );
        return $this->cod('SELECT codusuario FROM tblusuario WHERE correo = :c', [':c' => $correo]);
    }

    
    protected function barrio(): int
    {
        $this->db->insert("INSERT INTO tblcomuna (nombrecomuna) VALUES ('Comuna 1')");
        $comuna = $this->cod("SELECT MAX(codcomuna) FROM tblcomuna WHERE nombrecomuna = 'Comuna 1'");
        $this->db->insert("INSERT INTO tblbarrio (codcomuna, nombrebarrio) VALUES (:c, 'Barrio Centro')", [':c' => $comuna]);
        return $this->cod("SELECT MAX(codbarrio) FROM tblbarrio WHERE nombrebarrio = 'Barrio Centro'");
    }

    
    protected function tanque(): int
    {
        $coordinador = $this->usuario('Coordinador Control Biologico', 'carlos.rojas@ejemplo.com', '1098765003', '3001230003', 'A', 'Carlos', 'Rojas');
        $this->db->insert(
            "INSERT INTO tblzoocriadero (codusuario, codbarrio, nombrezoocriadero, direccion) VALUES (:u, :b, 'Zoocriadero Norte', 'Calle 10 # 5-20')",
            [':u' => $coordinador, ':b' => $this->barrio()]
        );
        $zoo = $this->cod("SELECT MAX(codzoocriadero) FROM tblzoocriadero WHERE nombrezoocriadero = 'Zoocriadero Norte'");

        
        $this->db->insert(
            "INSERT INTO public.tblzootanque (codtanque, codzoocriadero, codtipotanque, numerotanque, capacidad, fechacreacion, estado)
             VALUES (DEFAULT, :z, :t, 7, 500, DEFAULT, 'A')",
            [':z' => $zoo, ':t' => $this->cod("SELECT codtipotanque FROM tbltipotanque WHERE nombretipotanque = 'ALEVINES'")]
        );
        return $this->cod('SELECT codtanque FROM tblzootanque WHERE codzoocriadero = :z AND numerotanque = 7', [':z' => $zoo]);
    }
}
