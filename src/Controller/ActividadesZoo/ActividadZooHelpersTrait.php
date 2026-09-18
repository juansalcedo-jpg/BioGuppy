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
        $sql = "SELECT codtipoactividad FROM tbltipoactividadzoo WHERE nombreactividad ILIKE :nombre LIMIT 1";
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
