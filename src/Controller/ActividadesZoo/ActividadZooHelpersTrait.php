<?php

namespace BioGuppy\Controller\ActividadesZoo;

use PDO;

trait ActividadZooHelpersTrait
{
    private function consultarSeguro($obj, $sql, $params = [])
    {
        try {
            return $obj->select($sql, $params);
        } catch (\Throwable $error) {
            error_log("Consulta fallida en " . static::class . ": " . $error->getMessage());
            return false;
        }
    }

    private function obtenerCodTipoActividadZoo($obj, $nombre)
    {
        // Comparacion sin tildes ni mayusculas/minusculas, para evitar
        // problemas de codificacion entre PHP y PostgreSQL con palabras
        // como "Alimentacion" o "Recoleccion".
        $sql = "SELECT codtipoactividad
                FROM tbltipoactividadzoo
                WHERE UPPER(TRANSLATE(nombreactividad, 'ÁÉÍÓÚ', 'AEIOU')) = UPPER(:nombre)
                LIMIT 1";
        $resultado = $obj->select($sql, [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['codtipoactividad'] : null;
    }

    private function obtenerTanquesActivos($obj)
    {
        $sql = "SELECT tk.codtanque AS id, tk.numerotanque, z.nombrezoocriadero, tt.nombretipotanque
                FROM tblzootanque tk
                INNER JOIN tblzoocriadero z ON z.codzoocriadero = tk.codzoocriadero
                INNER JOIN tbltipotanque tt ON tt.codtipotanque = tk.codtipotanque
                WHERE tk.estado = 'A' AND z.estado = 'A'
                ORDER BY z.nombrezoocriadero ASC, tk.numerotanque ASC";
        return $this->consultarSeguro($obj, $sql);
    }
}
