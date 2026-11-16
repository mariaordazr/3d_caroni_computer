<?php
// app/config/bcv_rate.php

function getVenezuelaBcvRate(): ?array {
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    // 1. EXCLUSIVO PARA VENEZUELA: Si es Perú (corporaciongruponet.com), no mostrar nada
    if (strpos($host, 'corporaciongruponet.com') !== false) {
        return null;
    }

    date_default_timezone_set('America/Caracas');
    $today = date('Y-m-d'); // Cambia exactamente a las 12:00 AM hora de Venezuela

    $cacheDir = ROOT_PATH . 'public/assets/uploads/';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    $cacheFile = $cacheDir . 'bcv_rate_cache.json';

    $cache = [
        'active_rate'      => null, // Tasa vigente mostrada hoy
        'active_date'      => null, // Fecha valor de la tasa vigente (YYYY-MM-DD)
        'active_date_text' => '',   // Texto legible (ej. "16/11/2026")
        'staged_rate'      => null, // Tasa futura publicada un viernes para el lunes
        'staged_date'      => null, // Fecha valor futura (YYYY-MM-DD)
        'checked_for_day'  => null  // Día en que ya se verificó (se renueva a las 12:00 AM)
    ];

    if (file_exists($cacheFile)) {
        $loaded = json_decode(@file_get_contents($cacheFile), true);
        if (is_array($loaded)) {
            $cache = array_merge($cache, $loaded);
        }
    }

    // 2. Si había una tasa futura guardada (ej. publicada el viernes para el lunes)
    // y ya son las 12:00 AM o más del día correspondiente ($today >= staged_date), la activamos:
    if (!empty($cache['staged_rate']) && !empty($cache['staged_date']) && $cache['staged_date'] <= $today) {
        $cache['active_rate']      = (float)$cache['staged_rate'];
        $cache['active_date']      = $cache['staged_date'];
        $cache['active_date_text'] = date('d/m/Y', strtotime($cache['staged_date']));
        $cache['staged_rate']      = null;
        $cache['staged_date']      = null;
        @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // 3. Si hoy (después de las 12:00 AM) aún no hemos consultado, o no tenemos tasa activa
    if (($cache['checked_for_day'] ?? null) !== $today || empty($cache['active_rate'])) {
        $scraped = fetchFromBcvSources();

        if ($scraped && !empty($scraped['rate']) && !empty($scraped['date'])) {
            $bcvDate = $scraped['date']; // YYYY-MM-DD
            $bcvRate = (float)$scraped['rate'];

            if ($bcvDate <= $today) {
                // La fecha es igual o menor a hoy -> Se aplica de inmediato
                $cache['active_rate']      = $bcvRate;
                $cache['active_date']      = $bcvDate;
                $cache['active_date_text'] = date('d/m/Y', strtotime($bcvDate));
            } else {
                // La fecha es MAYOR a hoy (ej. Viernes publicando Lunes) -> Guardar en espera hasta el lunes a las 12:00 AM
                $cache['staged_rate'] = $bcvRate;
                $cache['staged_date'] = $bcvDate;

                // Si es la primera vez que se crea el caché y aún no existe tasa previa del viernes,
                // usamos la obtenida como base inicial para que nunca salga en 0
                if (empty($cache['active_rate'])) {
                    $cache['active_rate']      = $bcvRate;
                    $cache['active_date']      = $today;
                    $cache['active_date_text'] = date('d/m/Y', strtotime($today));
                }
            }

            $cache['checked_for_day'] = $today;
            @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // Siempre devolver estructura válida en Venezuela para que el botón NUNCA desaparezca
    $hasRate = !empty($cache['active_rate']) && (float)$cache['active_rate'] > 0;

    return [
        'raw'        => $hasRate ? (float)$cache['active_rate'] : 0,
        'formatted'  => $hasRate ? number_format((float)$cache['active_rate'], 2, ',', '.') : '...',
        'date'       => $cache['active_date'] ?? $today,
        'date_text'  => !empty($cache['active_date_text']) ? $cache['active_date_text'] : date('d/m/Y'),
        'needs_ajax' => !$hasRate
    ];
}

function fetchUrlContentBcv(string $url, int $timeout = 6): string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8',
                'Accept-Language: es-VE,es;q=0.9,en;q=0.8',
                'Cache-Control: no-cache'
            ],
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        ]);
        $res = @curl_exec($ch);
        curl_close($ch);
        if (!empty($res)) return (string)$res;
    }

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => $timeout,
            'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept-Language: es-VE,es;q=0.9\r\n"
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ]);
    return (string)@file_get_contents($url, false, $ctx);
}

function fetchFromBcvSources(): ?array {
    // CAPA 1: Sitio web directo del BCV (https://www.bcv.org.ve/)
    $html = fetchUrlContentBcv('https://www.bcv.org.ve/', 6);
    if (!empty($html)) {
        $rate = null;
        if (preg_match('/id=["\']dolar["\'][\s\S]*?<strong>\s*([0-9.,]+)\s*<\/strong>/i', $html, $m)) {
            $clean = str_replace('.', '', trim($m[1]));
            $clean = str_replace(',', '.', $clean);
            $rate = (float)$clean;
        }

        if ($rate && $rate > 0) {
            $bcvDate = null;
            if (preg_match('/property=["\']dc:date["\'][^>]*content=["\'](\d{4}-\d{2}-\d{2})/i', $html, $dm)) {
                $bcvDate = $dm[1];
            } elseif (preg_match('/content=["\'](\d{4}-\d{2}-\d{2})T/i', $html, $dm)) {
                $bcvDate = $dm[1];
            } elseif (preg_match('/Fecha\s+Valor[^0-9]*(\d{1,2})\s+(?:de\s+)?([A-Za-záéíóúÁÉÍÓÚ]+)\s+(?:de\s+)?(\d{4})/iu', $html, $tm)) {
                $day = str_pad($tm[1], 2, '0', STR_PAD_LEFT);
                $monthName = mb_strtolower(trim($tm[2]), 'UTF-8');
                $year = $tm[3];
                $months = [
                    'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
                    'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
                    'septiembre' => '09', 'setiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
                ];
                if (isset($months[$monthName])) {
                    $bcvDate = "{$year}-{$months[$monthName]}-{$day}";
                }
            }

            return [
                'rate' => $rate,
                'date' => $bcvDate ?: date('Y-m-d')
            ];
        }
    }

    // CAPA 2: Espejo Oficial BCV (DolarApi Venezuela) - Funciona desde hostings en EE.UU./Europa
    $jsonDolarApi = fetchUrlContentBcv('https://ve.dolarapi.com/v1/dolares/oficial', 5);
    if (!empty($jsonDolarApi)) {
        $data = json_decode($jsonDolarApi, true);
        if (is_array($data) && !empty($data['promedio']) && (float)$data['promedio'] > 0) {
            $apiDate = date('Y-m-d');
            if (!empty($data['fechaActualizacion']) && preg_match('/^(\d{4}-\d{2}-\d{2})/', $data['fechaActualizacion'], $fm)) {
                $apiDate = $fm[1];
            }
            return [
                'rate' => (float)$data['promedio'],
                'date' => $apiDate
            ];
        }
    }

    // CAPA 3: Segundo espejo Oficial BCV (PyDolarVenezuela)
    $jsonPyDolar = fetchUrlContentBcv('https://pydolarve.org/api/v1/dollar?page=bcv', 5);
    if (!empty($jsonPyDolar)) {
        $data2 = json_decode($jsonPyDolar, true);
        $usdPrice = $data2['monitors']['usd']['price'] ?? null;
        if (!empty($usdPrice) && (float)$usdPrice > 0) {
            return [
                'rate' => (float)$usdPrice,
                'date' => date('Y-m-d')
            ];
        }
    }

    return null;
}