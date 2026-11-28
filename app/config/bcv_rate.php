<?php
// app/config/bcv_rate.php

function getVenezuelaBcvRate(): ?array {
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    // 1. EXCLUSIVO PARA VENEZUELA: Si es Perú (corporaciongruponet.com), no mostrar nada
    if (strpos($host, 'corporaciongruponet.com') !== false) {
        return null;
    }

    date_default_timezone_set('America/Caracas');
    $today = date('Y-m-d');
    $now   = time();

    $cacheDir = ROOT_PATH . 'public/assets/uploads/';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    $cacheFile = $cacheDir . 'bcv_rate_cache.json';

    $cache = [
        'active_rate'      => null, // Tasa vigente mostrada hoy
        'active_date'      => null, // Fecha valor de la tasa vigente (YYYY-MM-DD)
        'active_date_text' => '',   // Texto legible (ej. "19/11/2026")
        'staged_rate'      => null, // Tasa futura publicada para el siguiente día hábil
        'staged_date'      => null, // Fecha valor futura (YYYY-MM-DD)
        'last_checked_ts'  => 0     // Timestamp Unix de la última consulta
    ];

    if (file_exists($cacheFile)) {
        $loaded = json_decode(@file_get_contents($cacheFile), true);
        if (is_array($loaded)) {
            $cache = array_merge($cache, $loaded);
        }
    }

    // 2. Si había una tasa futura guardada y ya pasó el día de la tasa activa (o llegamos a la fecha valor), la activamos
    $activeExpired = empty($cache['active_date']) || $cache['active_date'] < $today;
    if (!empty($cache['staged_rate']) && !empty($cache['staged_date']) && ($cache['staged_date'] <= $today || $activeExpired)) {
        $cache['active_rate']      = (float)$cache['staged_rate'];
        $cache['active_date']      = $cache['staged_date'];
        $cache['active_date_text'] = date('d/m/Y', strtotime($cache['staged_date']));
        $cache['staged_rate']      = null;
        $cache['staged_date']      = null;
        @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // 3. Consultar periódicamente (cada 10 min si aún tiene fecha vieja; si no, cada 30 min)
    $forceRefresh  = isset($_GET['refresh_bcv']) && $_GET['refresh_bcv'] == '1';
    $lastCheckedTs = (int)($cache['last_checked_ts'] ?? 0);
    $ttl           = (($cache['active_date'] ?? '') < $today) ? 600 : 1800;

    if ($forceRefresh || empty($cache['active_rate']) || ($now - $lastCheckedTs) >= $ttl) {
        $scraped = fetchFromBcvSources();

        if ($scraped && !empty($scraped['rate']) && !empty($scraped['date'])) {
            $bcvDate = $scraped['date']; // YYYY-MM-DD
            $bcvRate = (float)$scraped['rate'];
            $isActiveExpired = empty($cache['active_date']) || $cache['active_date'] < $today;

            if ($bcvDate <= $today || $isActiveExpired) {
                // Aplicar de inmediato si es de hoy/anterior, o si la tasa activa ya venció (ej. sábado/domingo/feriado)
                $cache['active_rate']      = $bcvRate;
                $cache['active_date']      = $bcvDate;
                $cache['active_date_text'] = date('d/m/Y', strtotime($bcvDate));
                $cache['staged_rate']      = null;
                $cache['staged_date']      = null;
            } else {
                // La fecha valor es FUTURA y la tasa de hoy sigue vigente -> Dejar en espera hasta medianoche
                $cache['staged_rate'] = $bcvRate;
                $cache['staged_date'] = $bcvDate;
            }
        }

        // Registrar el intento para no saturar si las fuentes fallan temporalmente
        $cache['last_checked_ts'] = $now;
        @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

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

            // Priorizar la Fecha Valor exacta del bloque de tasas
            if (preg_match('/Fecha\s+Valor[\s\S]{0,250}?content=["\'](\d{4}-\d{2}-\d{2})/i', $html, $dm)) {
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
            } elseif (preg_match('/class=["\'][^"\']*date-display-single[^"\']*["\'][^>]*content=["\'](\d{4}-\d{2}-\d{2})/i', $html, $dm)) {
                $bcvDate = $dm[1];
            } elseif (preg_match('/property=["\']dc:date["\'][^>]*content=["\'](\d{4}-\d{2}-\d{2})/i', $html, $dm)) {
                $bcvDate = $dm[1];
            }

            return [
                'rate' => $rate,
                'date' => $bcvDate ?: date('Y-m-d')
            ];
        }
    }

    // CAPA 2: Espejo Oficial BCV (DolarApi Venezuela)
    $jsonDolarApi = fetchUrlContentBcv('https://ve.dolarapi.com/v1/dolares/oficial', 5);
    if (!empty($jsonDolarApi)) {
        $data = json_decode($jsonDolarApi, true);
        if (is_array($data) && !empty($data['promedio']) && (float)$data['promedio'] > 0) {
            $apiDate = date('Y-m-d');
            if (!empty($data['fechaActualizacion'])) {
                try {
                    $dt = new DateTime($data['fechaActualizacion']);
                    $dt->setTimezone(new DateTimeZone('America/Caracas'));
                    $apiDate = $dt->format('Y-m-d');
                } catch (\Throwable $e) {
                    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $data['fechaActualizacion'], $fm)) {
                        $apiDate = $fm[1];
                    }
                }
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