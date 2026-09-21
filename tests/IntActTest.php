<?php

namespace BioGuppy\Tests;

use BioGuppy\Controller\ActividadesZoo\ActividadZooHelpersTrait;

require_once __DIR__ . '/BaseDB.php';


class IntActTest extends BaseDB
{
    
    private function zoo()
    {
        return new class {
            use ActividadZooHelpersTrait;
            public function llamar(string $metodo, ...$args) { return $this->$metodo(...$args); }
        };
    }

    // TI-04: un tanque registrado aparece en la lista de tanques del formulario Alimentación
    public function testTanque()
    {
        $tanque = $this->tanque();

        $fila = $this->uno(
            'SELECT t.numerotanque, t.capacidad, t.estado, z.nombrezoocriadero
             FROM tblzootanque t
             JOIN tblzoocriadero z ON z.codzoocriadero = t.codzoocriadero
             WHERE t.codtanque = :id',
            [':id' => $tanque]
        );

        $this->assertSame('Zoocriadero Norte', $fila['nombrezoocriadero']);
        $this->assertSame(7, (int) $fila['numerotanque']);
        $this->assertSame(500, (int) $fila['capacidad']);
        $this->assertSame('A', $fila['estado']);
    }

    // TI-05: una alimentación se guarda en la BD y queda en la auditoría
    public function testAlimenta()
    {
        $tanque = $this->tanque();
        $auxiliar = $this->usuario('Auxiliar Zoocriadero', 'maria.ruiz@ejemplo.com', '1098765004', '3001230004', 'A', 'María', 'Ruiz');

        
        $this->db->insert(
            "INSERT INTO public.tblactividadzoo
                (codactividad, codtipoactividad, codtanque, codusuario, fecha, horadia, tipopez, tipoalimento, fechacreacion, estado)
             VALUES (DEFAULT, :tipo, :tanque, :usu, CURRENT_DATE, 'MAÑANA', 'REPRODUCTOR', 'Concentrado', DEFAULT, DEFAULT)",
            [
                ':tipo'   => $this->zoo()->llamar('obtenerCodTipoActividadZoo', $this->db, 'ALIMENTACIÓN'),
                ':tanque' => $tanque,
                ':usu'    => $auxiliar,
            ]
        );

        $actividad = $this->uno('SELECT codactividad, estado FROM tblactividadzoo WHERE codtanque = :t', [':t' => $tanque]);
        $auditoria = $this->uno(
            'SELECT subactividades, codusuario FROM tblauditoriaactividadzoo WHERE codactividadprincipal = :id',
            [':id' => $actividad['codactividad']]
        );

        $this->assertSame('A', $actividad['estado']);
        $this->assertSame('MAÑANA,REPRODUCTOR,Concentrado', $auditoria['subactividades']);
        $this->assertSame($auxiliar, (int) $auditoria['codusuario']);
    }
}
