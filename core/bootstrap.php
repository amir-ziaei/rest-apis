<?php

use App\Core\App;
use App\Middleware\Auth;
use App\Core\Helpers\{session_helper, cookie_helper};
use App\Core\Database\{Connection, QueryBuilder};

App::bind('config', require 'config.php');

require 'core/functions.php';

App::bind('session', new session_helper);
App::bind('cookie', new cookie_helper);

App::bind('database', new QueryBuilder(
    Connection::make(App::get('config')['database'])
));