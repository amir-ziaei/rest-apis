
<?php

require 'vendor/autoload.php';
$query = require 'core/bootstrap.php';

use App\Core\Router;

$r = new Router;
$r->load('app/routes.php')
    ->dispatch($_SERVER['REQUEST_METHOD'],
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));