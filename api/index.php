<?php

/*
|--------------------------------------------------------------------------
| Vercel Serverless Entry Point
|--------------------------------------------------------------------------
|
| This file handles requests for Vercel serverless deployment by ensuring
| writable directories in /tmp and forwarding to public/index.php.
|
*/

$tmpStorage = '/tmp/storage';

if (!is_dir($tmpStorage . '/framework/views')) {
    @mkdir($tmpStorage . '/framework/views', 0755, true);
}
if (!is_dir($tmpStorage . '/framework/cache/data')) {
    @mkdir($tmpStorage . '/framework/cache/data', 0755, true);
}
if (!is_dir($tmpStorage . '/framework/sessions')) {
    @mkdir($tmpStorage . '/framework/sessions', 0755, true);
}
if (!is_dir($tmpStorage . '/logs')) {
    @mkdir($tmpStorage . '/logs', 0755, true);
}

// Override Laravel storage path for Vercel environment
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
putenv("APP_CONFIG_CACHE={$tmpStorage}/config.php");
putenv("APP_ROUTES_CACHE={$tmpStorage}/routes.php");
putenv("APP_SERVICES_CACHE={$tmpStorage}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpStorage}/packages.php");

// Force HTTPS: Vercel terminates SSL and forwards via X-Forwarded-Proto
// Without this, Laravel generates http:// URLs causing blank pages (mixed content)
if (
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// Set up SQLite database in /tmp if using SQLite
if (getenv('DB_CONNECTION') === 'sqlite' || !getenv('DB_CONNECTION')) {
    $sqliteDb = $tmpStorage . '/database.sqlite';
    if (!file_exists($sqliteDb)) {
        $sourceDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
            @copy($sourceDb, $sqliteDb);
        } else {
            @touch($sqliteDb);
        }
    }
    putenv("DB_DATABASE={$sqliteDb}");
}

require __DIR__ . '/../public/index.php';
