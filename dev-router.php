<?php
/**
 * Dev-only router for local preview: php -S localhost:8000 -t public dev-router.php
 * Not for production (Apache uses its own rewrite). Tooling approved by user 2026-10-20.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/public' . $path;
if ($path !== '/' && is_file($file)) {
    return false; // let the built-in server serve CSS/JS/images directly
}
require __DIR__ . '/public/index.php';
