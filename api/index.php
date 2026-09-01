<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup dynamic writable storage in /tmp for Vercel Serverless environment
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    $tmpStorage . '/app',
    $tmpStorage . '/app/public',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables for storage & views to use /tmp
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
putenv("APP_CONFIG_CACHE=/tmp/config.php");
putenv("APP_EVENTS_CACHE=/tmp/events.php");
putenv("APP_PACKAGES_CACHE=/tmp/packages.php");
putenv("APP_ROUTES_CACHE=/tmp/routes.php");
putenv("APP_SERVICES_CACHE=/tmp/services.php");
putenv("LOG_CHANNEL=stderr");

if (empty(getenv('SESSION_DRIVER'))) {
    putenv("SESSION_DRIVER=cookie");
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
}

if (empty(getenv('CACHE_STORE'))) {
    putenv("CACHE_STORE=array");
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';
}

// Maintenance check
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bind custom storage path to /tmp
$app->useStoragePath($tmpStorage);

// Handle request
$app->handleRequest(Request::capture());

