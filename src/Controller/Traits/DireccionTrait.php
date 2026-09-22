<?php

namespace BioGuppy\Controller\Traits;

/*
 * Autocompletado y validación de direcciones.
 * Lo usan ZoocriaderoController y SitiosController.
 *
 * - buscarDireccion(): se llama por AJAX (ajax.php) mientras el usuario escribe.
 *   Consulta Nominatim (OpenStreetMap) en formato XML y devuelve las opciones
 *   ya armadas en HTML (view/partials/sugerenciasDireccion.php).
 * - direccionValida(): valida el formato antes de guardar.
 */
trait DireccionTrait{

    // Formato permitido, ej: Calle 5 # 36-05 / Carrera 8A Bis # 3-15 Sur / Avenida Roosevelt # 38-20
    private function patronDireccion(){
        return '/^(Calle|Carrera|Avenida)\s+(\d{1,3}\s?[A-Z]?(\s?Bis)?(\s?[A-Z])?|[A-ZÁÉÍÓÚÑ]{3,}(\s[A-ZÁÉÍÓÚÑ]{2,}){0,3})(\s(Norte|Sur|Este|Oeste))?\s*#\s*\d{1,3}\s?[A-Z]?(\s?Bis)?\s*-\s*\d{1,3}(\s(Norte|Sur|Este|Oeste))?$/iu';
    }

    // Quita espacios repetidos y deja "Calle", "Carrera" o "Avenida" con mayúscula inicial
    private function normalizarDireccion($direccion){
        $direccion = preg_replace('/\s+/u', ' ', trim($direccion));

        return preg_replace_callback('/^(calle|carrera|avenida)/iu', function($via){
            return ucfirst(strtolower($via[1]));
        }, $direccion);
    }

    private function direccionValida($direccion){
        return preg_match($this->patronDireccion(), $direccion) === 1;
    }

    private function mensajeDireccionInvalida(){
        return "La dirección no es válida. Use el formato: Calle 5 # 36-05, Carrera 8A # 3-15 o Avenida Roosevelt # 38-20.";
    }

    public function buscarDireccion(){

        $texto   = trim($_GET['direccion'] ?? '');
        $mensaje = null;
        $sugerencias = [];

        // Solo se busca la vía (lo que está antes del #)
        $via = trim(explode('#', $texto)[0]);

        // Abreviaturas comunes: cl 5 -> Calle 5, cra 8 -> Carrera 8, av 6 -> Avenida 6
        $via = preg_replace(
            ['/^(clle|cll|cl)\.?\s*(?=\d)/iu', '/^(cra|kra|kr|cr)\.?\s*(?=\d)/iu', '/^(avda|av)\.?\s*(?=\d)/iu'],
            ['Calle ', 'Carrera ', 'Avenida '],
            $via
        );

        if(mb_strlen($via) < 4){
            $mensaje = "Escribe al menos 4 caracteres, por ejemplo: Calle 5.";
            include __DIR__ . '/../../../view/partials/sugerenciasDireccion.php';
            return;
        }

        // Guardamos en sesión lo que ya se buscó para no repetir peticiones a Nominatim
        $llave = mb_strtolower($via);

        if(isset($_SESSION['cacheDirecciones'][$llave])){
            $sugerencias = $_SESSION['cacheDirecciones'][$llave];
        }else{

            $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
                'q'               => $via,
                'format'          => 'xml',
                'addressdetails'  => 1,
                'limit'           => 10,
                'countrycodes'    => 'co',
                'viewbox'         => '-76.60,3.52,-76.45,3.32', // Santiago de Cali
                'bounded'         => 1,
                'accept-language' => 'es',
            ]);

            $respuesta = $this->consultarNominatim($url);

            if($respuesta === false){
                $mensaje = "No se pudo consultar el servicio de direcciones. Puedes escribirla manualmente (ej: Calle 5 # 36-05).";
                include __DIR__ . '/../../../view/partials/sugerenciasDireccion.php';
                return;
            }

            $xml = @simplexml_load_string($respuesta);
            $repetidas = [];

            if($xml){
                foreach($xml->place as $lugar){

                    $nombreVia = trim((string) $lugar->road);

                    // Solo vías que empiecen por Calle, Carrera o Avenida
                    if($nombreVia === '' || !preg_match('/^(Calle|Carrera|Avenida)\b/iu', $nombreVia)){
                        continue;
                    }

                    $barrio = trim((string) $lugar->neighbourhood);
                    if($barrio === '') $barrio = trim((string) $lugar->suburb);
                    if($barrio === '') $barrio = trim((string) $lugar->quarter);

                    $ciudad = trim((string) $lugar->city);
                    if($ciudad === '') $ciudad = 'Cali';

                    $clave = mb_strtolower($nombreVia . '|' . $barrio);
                    if(isset($repetidas[$clave])) continue;
                    $repetidas[$clave] = true;

                    // Si OSM trae la placa (ej: 36-05) se completa; si no, queda "Calle 5 # " para que la escriban
                    $placa = trim((string) $lugar->house_number);
                    $valor = $nombreVia . ' # ';
                    if(preg_match('/^\d{1,3}\s?[A-Za-z]?\s*-\s*\d{1,3}$/', $placa)){
                        $valor .= $placa;
                    }

                    $sugerencias[] = [
                        'via'    => $nombreVia,
                        'barrio' => $barrio,
                        'ciudad' => $ciudad,
                        'valor'  => $valor,
                    ];

                    if(count($sugerencias) >= 6) break;
                }
            }

            $_SESSION['cacheDirecciones'][$llave] = $sugerencias;
        }

        if(count($sugerencias) === 0){
            $mensaje = "No se encontraron vías con ese nombre en Cali. Puedes escribirla manualmente (ej: Calle 5 # 36-05).";
        }

        include __DIR__ . '/../../../view/partials/sugerenciasDireccion.php';
    }

    private function consultarNominatim($url){

        // Nominatim exige identificar la aplicación con un User-Agent
        $agente = 'BioGuppy/1.0 (bioguppy@gmail.com)';

        if(function_exists('curl_init')){
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT      => $agente,
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_CONNECTTIMEOUT => 4,
            ]);
            $respuesta = curl_exec($curl);

            if($respuesta === false){
                error_log("Error consultando Nominatim: " . curl_error($curl));
            }

            curl_close($curl);
            return $respuesta;
        }

        $contexto = stream_context_create([
            'http' => [
                'header'  => "User-Agent: $agente\r\n",
                'timeout' => 6,
            ],
        ]);

        return @file_get_contents($url, false, $contexto);
    }

}
