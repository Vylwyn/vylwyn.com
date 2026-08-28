<?php

/**
 * Production entry point for shared hosting where the document root is locked
 * to public_html and cannot be pointed at Laravel's own public/ directory.
 *
 * Server layout:
 *
 *   domains/vylwyn.com/
 *   ├── laravel/          the full application, outside the web root
 *   │   ├── vendor/
 *   │   ├── bootstrap/
 *   │   ├── storage/      never publicly reachable
 *   │   └── .env          never publicly reachable
 *   └── public_html/      the document root
 *       ├── index.php     this file
 *       ├── build/        compiled assets, copied from laravel/public
 *       └── <subdomains>  untouched by deploys
 *
 * Keeping the application above the document root is the whole point: a
 * misconfigured server can never serve .env or storage, because they are not
 * inside a directory the web server is willing to read from.
 *
 * This file is copied into place by the deploy workflow. Do not edit it on the
 * server — the next deploy will overwrite it.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$app_base = __DIR__.'/../laravel';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $app_base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $app_base.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $app_base.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
