<?php

use App\Core\App;
use App\Controller\{AuthController, UsersController, PostsController};

/*
 * Default Route
 */
$router->get('/', function(){
    return [
        "error" => "Not Found",
        "status" => 404,
        "message" => ""
    ];
});

/*
 * Middlewares
 */
$router->filter('auth', function(){
    if(App::get('session')->get('logged_in') !== true)
    {
        return [
            "error" => "Unauthorized",
            "status" => 401,
            "message" => ""
        ];
    }
});

$router->filter('guest', function(){
    if(App::get('session')->get('logged_in') === true)
    {
        return [
            "error" => "Not Found",
            "status" => 404,
            "message" => ""
        ];
    }
});

/*
 * API V1 Routes
 */
$router->group(['prefix' => 'api/v1'], function($router){

    /*
     * Posts Routes
     */
    $router->group(['prefix' => 'posts'], function($router){

        $router->get('', function() {
            return (new PostsController)->index();
        });

        $router->post('', function() {
            return (new PostsController)->create();
        });

        $router->get('{id:\d+}', function($id) {
            return (new PostsController)->show($id);
        });

        $router->put('{id:\d+}', function($id) {
            return (new PostsController)->update($id);
        });

        $router->delete('{id:\d+}', function($id) {
            return (new PostsController)->delete($id);
        });
    });

    /*
     * Users Routes
     */
    $router->group(['prefix' => 'users'], function($router){

        $router->group(['before' => 'guest'], function ($router){
            $router->post('', function() {
                return (new UsersController)->create();
            });
            $router->post('login', function() {
                return (new AuthController)->login();
            });
        });

        $router->group(['before' => 'auth'], function ($router){
            $router->get('', function() {
                return (new UsersController)->index();
            });
            $router->get('{id:\d+}', function($id) {
                return (new UsersController)->show($id);
            });
            $router->put('{id:\d+}', function($id) {
                return (new UsersController)->update($id);
            });
            $router->delete('{id:\d+}', function($id) {
                return (new UsersController)->delete($id);
            });
        });

    });
});