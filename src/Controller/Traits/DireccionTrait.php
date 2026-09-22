<?php

namespace BioGuppy\Controller\Traits;

/*
 * Autocompletado y validación de direcciones.
 * Lo usan ZoocriaderoController y SitiosController.
 *
 * - buscarDireccion(): se llama por AJAX (ajax.php) mientras el usuario escribe.
 *     · "Calle 5"            -> opciones de la vía en distintos barrios
 *     · "Calle 5 # 36-05"    -> una sola opción con la dirección completa y su barrio
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

    // Abreviaturas comunes: cl 5 -> Calle 5, cra 8 -> Carrera 8, av 6 -> Avenida 6
    private function expandirAbreviaturas($texto){
        return preg_replace(
            ['/^(clle|cll|cl)\.?\s*(?=\d)/iu', '/^(cra|kra|kr|cr)\.?\s*(?=\d)/iu', '/^(avda|av)\.?\s*(?=\d)/iu'],
            ['Calle ', 'Carrera ', 'Avenida '],
            $texto
        );
    }

    // Texto para comparar nombres sin importar tildes, mayúsculas ni espacios
    private function textoComparable($texto){
        $texto = mb_strtolower(trim($texto));
        $texto = strtr($texto, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u']);
        return preg_replace('/\s+/u', ' ', $texto);
    }

    public function buscarDireccion(){

        $texto = $this->expandirAbreviaturas(preg_replace('/\s+/u', ' ', trim($_GET['direccion'] ?? '')));

        $partes = explode('#', $texto, 2);
        $via    = $this->normalizarDireccion($partes[0]);
        $placa  = isset($partes[1]) ? trim($partes[1]) : null;

        $mensaje     = null;
        $sugerencias = [];

        if(mb_strlen($via) < 4){
            $mensaje = "Escribe al menos 4 caracteres, por ejemplo: Calle 5.";
            include __DIR__ . '/../../../view/partials/sugerenciasDireccion.php';
            return;
        }

        $lugares = $this->buscarVias($via);

        if($lugares === false){
            $mensaje = "No se pudo consultar el servicio de direcciones. Puedes escribirla manualmente (ej: Calle 5 # 36-05).";
        }elseif(count($lugares) === 0){
            $mensaje = "No se encontró esa vía en Cali. Puedes escribirla manualmente (ej: Calle 5 # 36-05).";
        }elseif($placa !== null && preg_match('/^(\d{1,3})\s?([A-Za-z](?![A-Za-z]))?(\s?Bis)?\s*(-\s*(\d{1,3})?)?\s*(Norte|Sur|Este|Oeste)?/iu', $placa, $m)){
            // Ya escribió la placa (o parte): una sola opción con la dirección completa
            $sugerencias = $this->opcionDireccionCompleta($via, $m, $lugares);
        }else{
            // Solo escribió la vía: opciones de esa vía en distintos barrios
            $sugerencias = $this->opcionesPorVia($lugares);
        }

        include __DIR__ . '/../../../view/partials/sugerenciasDireccion.php';
    }

    private function opcionesPorVia($lugares){

        $sugerencias = [];
        $repetidas   = [];

        foreach($lugares as $lugar){
            $clave = $this->textoComparable($lugar['via'] . '|' . $lugar['barrio']);
            if(isset($repetidas[$clave])) continue;
            $repetidas[$clave] = true;

            $sugerencias[] = [
                'titulo'  => $lugar['via'],
                'barrio'  => $lugar['barrio'],
                'ciudad'  => $lugar['ciudad'],
                'detalle' => null,
                'valor'   => $lugar['via'] . ' # ',
            ];

            if(count($sugerencias) >= 6) break;
        }

        return $sugerencias;
    }

    private function opcionDireccionCompleta($via, $m, $lugares){

        // Partes de la placa: "36A Bis - 05 Sur"
        $numeroCruce = $m[1] . (!empty($m[2]) ? strtoupper($m[2]) : '') . (!empty($m[3]) ? ' Bis' : '');
        $numeroCasa  = $m[5] ?? '';
        $cardinal    = !empty($m[6]) ? ' ' . ucfirst(strtolower($m[6])) : '';
        $completa    = $numeroCasa !== '';

        // Si la vía escrita coincide exacto con una de OSM, usamos solo esos tramos
        $iguales = array_values(array_filter($lugares, function($l) use ($via){
            return $this->textoComparable($l['via']) === $this->textoComparable($via);
        }));
        $tramos    = $iguales;
        $nombreVia = count($iguales) > 0 ? $iguales[0]['via'] : $via;

        $valor = $nombreVia . ' # ' . $numeroCruce . '-' . $numeroCasa . $cardinal;

        // Calle 5 # 36-05 queda sobre la Calle 5, a la altura de la Carrera 36 (y al revés)
        $tipoVia   = $this->textoComparable(explode(' ', $nombreVia)[0]);
        $tipoCruce = $tipoVia === 'calle' ? 'Carrera' : ($tipoVia === 'carrera' ? 'Calle' : null);

        $barrio  = '';
        $detalle = null;

        if($tipoCruce && count($tramos) > 0){
            $viaCruce = $tipoCruce . ' ' . $numeroCruce;
            $cruces   = $this->buscarVias($viaCruce);

            if($cruces){
                $crucesIguales = array_values(array_filter($cruces, function($l) use ($viaCruce){
                    return $this->textoComparable($l['via']) === $this->textoComparable($viaCruce);
                }));

                if(count($crucesIguales) > 0){
                    $punto = $this->puntoMasCercano($tramos, $crucesIguales);

                    if($punto){
                        $barrio  = $this->barrioEnPunto($punto['lat'], $punto['lon']);
                        if($barrio === '') $barrio = $punto['barrio'];
                        $detalle = 'Cerca del cruce con ' . $crucesIguales[0]['via'];
                    }
                }
            }
        }

        // Si no se pudo ubicar el cruce y la vía solo tiene un barrio, usamos ese
        if($barrio === '' && count($tramos) === 1){
            $barrio = $tramos[0]['barrio'];
        }

        if(!$completa){
            $detalle = 'Completa la placa, ej: ' . $nombreVia . ' # ' . $numeroCruce . '-05';
        }elseif(count($tramos) === 0){
            $detalle = 'No encontramos esa vía exacta en el mapa; revisa que esté bien escrita';
        }elseif($barrio === '' && $detalle === null){
            $detalle = 'No se pudo identificar el barrio';
        }

        return [[
            'titulo'  => $valor,
            'barrio'  => $barrio,
            'ciudad'  => 'Cali',
            'detalle' => $detalle,
            'valor'   => $valor,
        ]];
    }

    // Busca el tramo de la vía que queda más cerca de algún tramo de la vía que la cruza
    private function puntoMasCercano($tramos, $cruces){

        $mejor = null;
        $menorDistancia = null;

        foreach($tramos as $t){
            foreach($cruces as $c){
                $dLat = $t['lat'] - $c['lat'];
                $dLon = ($t['lon'] - $c['lon']) * cos(deg2rad($t['lat']));
                $distancia = sqrt($dLat * $dLat + $dLon * $dLon) * 111; // km aprox.

                if($menorDistancia === null || $distancia < $menorDistancia){
                    $menorDistancia = $distancia;
                    $mejor = [
                        'lat'    => ($t['lat'] + $c['lat']) / 2,
                        'lon'    => ($t['lon'] + $c['lon']) / 2,
                        'barrio' => $t['barrio'],
                    ];
                }
            }
        }

        // Si quedan a más de 2 km no se consideran un cruce
        return ($menorDistancia !== null && $menorDistancia <= 2) ? $mejor : null;
    }

    // Busca una vía en Cali. Devuelve sus tramos (con barrio y coordenadas) o false si falla el servicio
    private function buscarVias($via){

        $llave = 'via:' . $this->textoComparable($via);

        if(isset($_SESSION['cacheDirecciones'][$llave])){
            return $_SESSION['cacheDirecciones'][$llave];
        }

        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q'               => $via,
            'format'          => 'xml',
            'addressdetails'  => 1,
            'limit'           => 40,
            'countrycodes'    => 'co',
            'viewbox'         => '-76.60,3.52,-76.45,3.32', // Santiago de Cali
            'bounded'         => 1,
            'accept-language' => 'es',
        ]);

        $respuesta = $this->consultarNominatim($url);
        if($respuesta === false) return false;

        $xml = @simplexml_load_string($respuesta);
        if(!$xml) return false;

        $lugares = [];

        foreach($xml->place as $lugar){

            $nombreVia = trim((string) $lugar->road);

            // Solo vías que empiecen por Calle, Carrera o Avenida
            if($nombreVia === '' || !preg_match('/^(Calle|Carrera|Avenida)\b/iu', $nombreVia)){
                continue;
            }

            $lugares[] = [
                'via'    => $nombreVia,
                'barrio' => $this->barrioDeXml($lugar),
                'ciudad' => trim((string) $lugar->city) !== '' ? trim((string) $lugar->city) : 'Cali',
                'lat'    => (float) $lugar['lat'],
                'lon'    => (float) $lugar['lon'],
            ];
        }

        $_SESSION['cacheDirecciones'][$llave] = $lugares;
        return $lugares;
    }

    // Barrio de un punto (geocodificación inversa)
    private function barrioEnPunto($lat, $lon){

        $llave = 'punto:' . round($lat, 4) . ',' . round($lon, 4);

        if(isset($_SESSION['cacheDirecciones'][$llave])){
            return $_SESSION['cacheDirecciones'][$llave];
        }

        $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
            'lat'             => $lat,
            'lon'             => $lon,
            'format'          => 'xml',
            'zoom'            => 17,
            'addressdetails'  => 1,
            'accept-language' => 'es',
        ]);

        $respuesta = $this->consultarNominatim($url);
        $xml = $respuesta ? @simplexml_load_string($respuesta) : false;

        if(!$xml || !isset($xml->addressparts)) return '';

        $barrio = $this->barrioDeXml($xml->addressparts);
        $_SESSION['cacheDirecciones'][$llave] = $barrio;

        return $barrio;
    }

    private function barrioDeXml($nodo){
        foreach(['neighbourhood', 'suburb', 'quarter'] as $campo){
            $valor = trim((string) $nodo->$campo);
            if($valor !== '') return $valor;
        }
        return '';
    }

    private function consultarNominatim($url){

        // Nominatim permite máximo 1 petición por segundo: si la anterior fue hace
        // menos de 1 segundo, esperamos lo que falte
        $ultima = $_SESSION['ultimaConsultaNominatim'] ?? 0;
        $espera = 1.0 - (microtime(true) - $ultima);
        if($espera > 0){
            usleep((int) ($espera * 1000000));
        }

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
        }else{
            $contexto = stream_context_create([
                'http' => [
                    'header'  => "User-Agent: $agente\r\n",
                    'timeout' => 6,
                ],
            ]);
            $respuesta = @file_get_contents($url, false, $contexto);
        }

        $_SESSION['ultimaConsultaNominatim'] = microtime(true);

        return $respuesta;
    }

}
