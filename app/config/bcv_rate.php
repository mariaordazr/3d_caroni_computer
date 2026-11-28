<?php
// app/config/bcv_rate.php

function getVenezuelaBcvRate(): ?array {
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
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
        'active_rate'      => null,
        'active_date'      => null,
        'active_date_text' => '',
        'staged_rate'      => null,
        'staged_date'      => null,
        'last_checked_ts'  => 0,
        'last_source'      => ''
    ];

    if (file_exists($cacheFile)) {
        $loaded = json_decode(@file_get_contents($cacheFile), true);
        if (is_array($loaded)) {
            $cache = array_merge($cache, $loaded);
        }
    }

    // 1. Promover tasa futura en espera si ya llegó su fecha o si la tasa activa ya venció (sábado/domingo/feriado)
    $activeExpired = empty($cache['active_date']) || $cache['active_date'] < $today;
    if (!empty($cache['staged_rate']) && !empty($cache['staged_date']) && ($cache['staged_date'] <= $today || $activeExpired)) {
        $cache['active_rate']      = (float)$cache['staged_rate'];
        $cache['active_date']      = $cache['staged_date'];
        $cache['active_date_text'] = date('d/m/Y', strtotime($cache['staged_date']));
        $cache['staged_rate']      = null;
        $cache['staged_date']      = null;
        @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $forceRefresh  = (isset($_GET['refresh_bcv']) && $_GET['refresh_bcv'] == '1') || isset($_GET['debug_bcv']);
    $lastCheckedTs = (int)($cache['last_checked_ts'] ?? 0);
    $ttl           = (($cache['active_date'] ?? '') < $today) ? 600 : 1800;
    $debugLog      = [];

    if ($forceRefresh || empty($cache['active_rate']) || ($now - $lastCheckedTs) >= $ttl) {
        $scraped = fetchFromBcvSources($debugLog);

        if ($scraped && !empty($scraped['rate']) && !empty($scraped['date'])) {
            $bcvDate = $scraped['date'];
            $bcvRate = (float)$scraped['rate'];
            $currentActiveDate = $cache['active_date'] ?? '';

            // PROTECCIÓN: Nunca sobrescribir con una fecha más vieja que la tasa ya guardada
            if ($currentActiveDate === '' || $bcvDate >= $currentActiveDate) {
                $isActiveExpired = empty($currentActiveDate) || $currentActiveDate < $today;

                if ($bcvDate <= $today || $isActiveExpired) {
                    $cache['active_rate']      = $bcvRate;
                    $cache['active_date']      = $bcvDate;
                    $cache['active_date_text'] = date('d/m/Y', strtotime($bcvDate));
                    $cache['staged_rate']      = null;
                    $cache['staged_date']      = null;
                    $cache['last_source']      = $scraped['source'] ?? '';
                } else {
                    $cache['staged_rate'] = $bcvRate;
                    $cache['staged_date'] = $bcvDate;
                    $cache['last_source'] = $scraped['source'] ?? '';
                }
            }
        }

        $cache['last_checked_ts'] = $now;
        @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Diagnóstico rápido en consola del navegador si se abre ?debug_bcv=1
    if (isset($_GET['debug_bcv']) && $_GET['debug_bcv'] == '1') {
        echo '<script>console.log("BCV_DEBUG", ' . json_encode([
            'today' => $today,
            'cache' => $cache,
            'log'   => $debugLog
        ], JSON_UNESCAPED_UNICODE) . ');</script>';
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

function fetchUrlContentBcv(string $url, int $timeout = 10, ?string &$errOut = null): string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 7,
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8',
                'Accept-Language: es-VE,es;q=0.9,en;q=0.8',
                'Cache-Control: no-cache'
            ],
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        ]);
        $res  = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res !== false && !empty($res) && $code >= 200 && $code < 400) {
            return (string)$res;
        }
        $errOut = "HTTP {$code} | {$err}";
        return '';
    }

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => $timeout,
            'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept-Language: es-VE,es;q=0.9\r\n"
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $res = @file_get_contents($url, false, $ctx);
    return $res !== false ? (string)$res : '';
}

function fetchFromBcvSources(array &$debugLog = []): ?array {
    // CAPA 1: Sitio oficial del BCV
    foreach (['https://www.bcv.org.ve/', 'http://www.bcv.org.ve/'] as $bcvUrl) {
        $err  = null;
        $html = fetchUrlContentBcv($bcvUrl, 10, $err);

        if (!empty($html)) {
            $rate    = null;
            $bcvDate = null;

            // 1A. Extraer con DOMDocument / XPath (más seguro que regex en HTML de >150KB)
            if (class_exists('DOMDocument')) {
                libxml_use_internal_errors(true);
                $dom = new DOMDocument();
                @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
                libxml_clear_errors();
                $xpath = new DOMXPath($dom);

                $dolarNode = $xpath->query("//*[@id='dolar']")->item(0);
                if ($dolarNode) {
                    $text = preg_replace('/\s+/', ' ', trim($dolarNode->textContent));
                    if (preg_match('/(\d{1,3}(?:\.\d{3})*,\d{2,8}|\d+,\d{2,8})/', $text, $nm)) {
                        $rate = (float)str_replace(',', '.', str_replace('.', '', $nm[1]));
                    }
                }

                // Fecha valor desde la clase estándar de Drupal del BCV
                $dateNode = $xpath->query("//*[contains(@class,'date-display-single')]")->item(0);
                if ($dateNode) {
                    $contentAttr = $dateNode->attributes ? $dateNode->attributes->getNamedItem('content') : null;
                    if ($contentAttr && preg_match('/^(\d{4}-\d{2}-\d{2})/', $contentAttr->nodeValue, $dm)) {
                        $bcvDate = $dm[1];
                    }
                }
            }

            // 1B. Respaldo regex sobre ventana acotada (evita límite de backtrack en HTML grande)
            if (!$rate) {
                if (preg_match('/id=["\']dolar["\'](.{0,1500})/is', $html, $blk)) {
                    $plain = strip_tags($blk[1]);
                    if (preg_match('/(\d{1,3}(?:\.\d{3})*,\d{2,8}|\d+,\d{2,8})/', $plain, $nm)) {
                        $rate = (float)str_replace(',', '.', str_replace('.', '', $nm[1]));
                    }
                } elseif (preg_match('/\bUSD\b(.{0,400})/is', $html, $blk)) {
                    $plain = strip_tags($blk[1]);
                    if (preg_match('/(\d{1,3}(?:\.\d{3})*,\d{2,8}|\d+,\d{2,8})/', $plain, $nm)) {
                        $rate = (float)str_replace(',', '.', str_replace('.', '', $nm[1]));
                    }
                }
            }

            if ($rate && $rate > 0) {
                if (!$bcvDate) {
                    if (preg_match('/Fecha\s+Valor[\s\S]{0,300}?content=["\'](\d{4}-\d{2}-\d{2})/i', $html, $dm)) {
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
                }

                $debugLog[] = "OK {$bcvUrl}: rate={$rate}, date=" . ($bcvDate ?: 'fallback-today');
                return [
                    'rate'   => $rate,
                    'date'   => $bcvDate ?: date('Y-m-d'),
                    'source' => $bcvUrl
                ];
            } else {
                // Guardar muestra pequeña para diagnóstico si vuelve a fallar
                $pos = stripos($html, 'dolar');
                if ($pos === false) $pos = stripos($html, 'USD');
                $sample = $pos !== false ? substr($html, max(0, $pos - 60), 320) : substr(strip_tags($html), 0, 220);
                $debugLog[] = "FAIL parse {$bcvUrl} (len=" . strlen($html) . ") sample=" . preg_replace('/\s+/', ' ', $sample);
            }
        } else {
            $debugLog[] = "FAIL fetch {$bcvUrl}: {$err}";
        }
    }

    // CAPA 2: DolarApi Venezuela
    $err2 = null;
    $jsonDolarApi = fetchUrlContentBcv('https://ve.dolarapi.com/v1/dolares/oficial', 7, $err2);
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
            $debugLog[] = "OK dolarapi: rate={$data['promedio']}, date={$apiDate}";
            return [
                'rate'   => (float)$data['promedio'],
                'date'   => $apiDate,
                'source' => 'dolarapi'
            ];
        }
    } else {
        $debugLog[] = "FAIL dolarapi: {$err2}";
    }

    // CAPA 3: PyDolarVenezuela
    $err3 = null;
    $jsonPyDolar = fetchUrlContentBcv('https://pydolarve.org/api/v1/dollar?page=bcv', 7, $err3);
    if (!empty($jsonPyDolar)) {
        $data2 = json_decode($jsonPyDolar, true);
        $usdPrice = $data2['monitors']['usd']['price'] ?? null;
        if (!empty($usdPrice) && (float)$usdPrice > 0) {
            $debugLog[] = "OK pydolar: rate={$usdPrice}";
            return [
                'rate'   => (float)$usdPrice,
                'date'   => date('Y-m-d'),
                'source' => 'pydolar'
            ];
        }
    } else {
        $debugLog[] = "FAIL pydolar: {$err3}";
    }

    return null;
}