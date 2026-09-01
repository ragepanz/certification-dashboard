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

/**
 * Force-set all critical environment variables for Vercel Serverless.
 * Uses all three methods (putenv, $_ENV, $_SERVER) to guarantee
 * Laravel's env() helper reads them regardless of runtime context.
 */
function vercelEnv(string $key, string $value): void
{
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Storage & caching paths -> /tmp (writable)
vercelEnv('VIEW_COMPILED_PATH', $tmpStorage . '/framework/views');
vercelEnv('APP_CONFIG_CACHE', '/tmp/config.php');
vercelEnv('APP_EVENTS_CACHE', '/tmp/events.php');
vercelEnv('APP_PACKAGES_CACHE', '/tmp/packages.php');
vercelEnv('APP_ROUTES_CACHE', '/tmp/routes.php');
vercelEnv('APP_SERVICES_CACHE', '/tmp/services.php');

// Drivers that MUST NOT require a database connection on cold start
vercelEnv('LOG_CHANNEL', 'stderr');
vercelEnv('SESSION_DRIVER', 'cookie');
vercelEnv('CACHE_STORE', 'array');
vercelEnv('QUEUE_CONNECTION', 'sync');
vercelEnv('BROADCAST_CONNECTION', 'log');
vercelEnv('FILESYSTEM_DISK', 'local');

// Pass DB credentials explicitly from Vercel system environment
if ($dbHost = getenv('DB_HOST')) vercelEnv('DB_HOST', $dbHost);
if ($dbPort = getenv('DB_PORT')) vercelEnv('DB_PORT', $dbPort);
if ($dbDatabase = getenv('DB_DATABASE')) vercelEnv('DB_DATABASE', $dbDatabase);
if ($dbUsername = getenv('DB_USERNAME')) vercelEnv('DB_USERNAME', $dbUsername);
if ($dbPassword = getenv('DB_PASSWORD')) vercelEnv('DB_PASSWORD', $dbPassword);
if ($dbConn = getenv('DB_CONNECTION')) vercelEnv('DB_CONNECTION', $dbConn);
if ($appKey = getenv('APP_KEY')) vercelEnv('APP_KEY', $appKey);

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
