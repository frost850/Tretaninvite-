<?php
// FILE INI diletakkan di: public_html/index.php
// Project Laravel ditempatkan di: /home/trew8324/laravel/  (LUAR public_html)

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require __DIR__.'/../laravel/vendor/autoload.php';

// Bootstrap & handle request
(require_once __DIR__.'/../laravel/bootstrap/app.php')
    ->handleRequest(Request::capture());
