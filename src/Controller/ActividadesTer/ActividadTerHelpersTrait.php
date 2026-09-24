<?php

namespace BioGuppy\Controller\ActividadesTer;

use PDO;
trait ActividadTerHelpersTrait
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

    private function error($mensaje, $funcion, $params = [])
    {
        $_SESSION['error'] = $mensaje;
        redirect(getUrl(static::RUTA_MODULO, static::RUTA_CONTROLADOR, $funcion, $params));
        exit();
    }

    private function obtenerDepositosActivos($obj)
    {
        return $this->consultarSeguro($obj, "SELECT s.codsitio AS id,s.nombresitio,td.nombretipodeposito AS tipodeposito
        FROM tblsitio s
        INNER JOIN tbltipodeposito td ON td.codtipodeposito=s.codtipodeposito
        WHERE s.estado='A' AND td.estado='A'
        ORDER BY s.nombresitio ASC");
    }

    private function obtenerCodTipoActividad($obj, $nombre)
    {
        $resultado = $obj->select("SELECT codtipoactividad
        FROM tbltipoactividadterreno
        WHERE TRANSLATE(UPPER(nombreactividad),'ÁÉÍÓÚ','AEIOU')=TRANSLATE(UPPER(:nombre),'ÁÉÍÓÚ','AEIOU')
        LIMIT 1", [':nombre' => $nombre])->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['codtipoactividad'] : null;
    }

    private function detectarTipo($nombre)
    {
        $tipo = strtoupper($nombre);
        if (strpos($tipo, 'RESIEMBRA') !== false)
            return 'Resiembra';
        if (strpos($tipo, 'INSPEC') !== false)
            return 'Inspeccion';
        if (strpos($tipo, 'SEGUIMIENTO') !== false)
            return 'Seguimiento';
        if (strpos($tipo, 'SIEMBRA') !== false)
            return 'Siembra';
        return null;
    }

    private function validarFechaHora($fecha, $hora, $funcion, $params = [], $ultimosDosDias = true)
    {
        if (empty($fecha))
            $this->error("Debe registrar la fecha de la actividad.", $funcion, $params);

        if ($ultimosDosDias) {
            $min = date('Y-m-d', strtotime('-2 days'));
            $max = date('Y-m-d');
            if ($fecha < $min || $fecha > $max)
                $this->error("Solo puede registrar actividades de hoy o de los últimos 2 días.", $funcion, $params);
        } elseif ($fecha > date('Y-m-d')) {
            $this->error("La fecha no puede ser mayor a la fecha actual.", $funcion, $params);
        }

        if (empty($hora))
            $this->error("Debe registrar la hora de la actividad.", $funcion, $params);
        if (!preg_match('/^(0[8-9]|1[0-7]):[0-5][0-9]$|^18:00$/', $hora)) {
            $this->error("La hora debe estar entre las 08:00 y las 18:00.", $funcion, $params);
        }
    }

    private function validarEntero($valor, $nombre, $funcion, $params = [])
    {
        if ($valor === null || $valor === '')
            $this->error("Debe ingresar " . $nombre . ".", $funcion, $params);
        if (is_numeric($valor) && $valor < 0)
            $this->error(ucfirst($nombre) . " no puede ser un número negativo.", $funcion, $params);
        if (!preg_match('/^(0|[1-9][0-9]*)$/', (string) $valor)) {
            $this->error(ucfirst($nombre) . " debe ser un número entero sin ceros a la izquierda.", $funcion, $params);
        }
    }

    private function datosActividad($tipo, $fuente, $funcion, $params = [], $actual = [])
    {
        $datos = [
            'ph' => $actual['ph'] ?? null,
            'temperatura' => $actual['temperatura'] ?? null,
            'larvasaedes' => $actual['larvasaedes'] ?? 0,
            'pupas' => $actual['pupas'] ?? 0,
            'larvasculex' => $actual['larvasculex'] ?? 0,
            'peces' => $actual['peces'] ?? null,
            'larvas' => $actual['larvas'] ?? null,
            'cantidadhembras' => $actual['cantidadhembras'] ?? null,
            'cantidadmachos' => $actual['cantidadmachos'] ?? null,
            'tiempoaclimatacionmin' => $actual['tiempoaclimatacionmin'] ?? null,
            'volumenagualitros' => $actual['volumenagualitros'] ?? null,
            'recolectarempacar' => $actual['recolectarempacar'] ?? null
        ];

        if ($tipo === 'Inspeccion') {
            $ph = $fuente['ph'] ?? null;
            $temperatura = $fuente['temperatura'] ?? null;
            $aedes = $fuente['larvas_aedes'] ?? null;
            $pupas = $fuente['pupas'] ?? null;
            $culex = $fuente['larvas_culex'] ?? null;

            if ($ph === null || $ph === '')
                $this->error("Debe ingresar el pH del agua.", $funcion, $params);
            if (!is_numeric($ph) || $ph < 0 || $ph > 14)
                $this->error("El pH debe estar entre 0 y 14.", $funcion, $params);
            if ($temperatura === null || $temperatura === '')
                $this->error("Debe ingresar la temperatura.", $funcion, $params);
            if (!is_numeric($temperatura) || $temperatura < 0 || $temperatura > 40)
                $this->error("La temperatura debe estar entre 0°C y 40°C.", $funcion, $params);

            $this->validarEntero($aedes, 'la cantidad de larvas Aedes', $funcion, $params);
            $this->validarEntero($pupas, 'la cantidad de pupas', $funcion, $params);
            $this->validarEntero($culex, 'la cantidad de larvas Culex', $funcion, $params);

            $datos['ph'] = $ph;
            $datos['temperatura'] = $temperatura;
            $datos['larvasaedes'] = $aedes;
            $datos['pupas'] = $pupas;
            $datos['larvasculex'] = $culex;
        }

        if ($tipo === 'Siembra' || $tipo === 'Resiembra') {
            $hembras = $fuente['cantidad_hembras'] ?? null;
            $machos = $fuente['cantidad_machos'] ?? null;
            $tiempo = $fuente['tiempo_aclimatar'] ?? null;
            $recolectar = $fuente['recolectar_empacar'] ?? null;

            $this->validarEntero($hembras, 'la cantidad de hembras', $funcion, $params);
            $this->validarEntero($machos, 'la cantidad de machos', $funcion, $params);
            $this->validarEntero($tiempo, 'el tiempo de aclimatación', $funcion, $params);

            if (((int) $hembras + (int) $machos) <= 0)
                $this->error("Debe registrar al menos un guppy, hembra o macho.", $funcion, $params);
            if ($recolectar !== 'S' && $recolectar !== 'N')
                $this->error("Debe seleccionar si se recolecta y empaca.", $funcion, $params);

            $datos['cantidadhembras'] = $hembras;
            $datos['cantidadmachos'] = $machos;
            $datos['tiempoaclimatacionmin'] = $tiempo;
            $datos['recolectarempacar'] = $recolectar;

            if ($tipo === 'Siembra') {
                $volumen = $fuente['volumen_agua'] ?? null;
                $this->validarEntero($volumen, 'el volumen de agua', $funcion, $params);
                $datos['volumenagualitros'] = $volumen;
            }
        }

        if ($tipo === 'Seguimiento') {
            $peces = $fuente['peces'] ?? null;
            $larvas = $fuente['larvas'] ?? null;
            if ($peces !== 'S' && $peces !== 'N')
                $this->error("Debe seleccionar si se evidencia presencia de peces.", $funcion, $params);
            if ($larvas !== 'S' && $larvas !== 'N')
                $this->error("Debe seleccionar si se evidencia presencia de larvas.", $funcion, $params);
            $datos['peces'] = $peces;
            $datos['larvas'] = $larvas;
        }

        return $datos;
    }
}
