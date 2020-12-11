<?php

use App\Core\App;
use App\Controller\{AuthController, CategoriesController, UsersController, PostsController};

// -----------------------------------------------------------------------
/*
 * Default Route
 */
// -----------------------------------------------------------------------
$router->any('/', function(){
    return [
        "error" => "Not Found",
        "status" => 404,
        "message" => ""
    ];
});
// -----------------------------------------------------------------------
/*
 * Middlewares
 */
// -----------------------------------------------------------------------
$router->filter('auth', function(){
    if( ! App::get('session')->get('logged_in'))
    {
        $user_id = App::get('cookie')->get('user_id');
        $token = App::get('cookie')->get('token');
        if($user_id && $token) {
            $token_exists_and_matches = App::get('database')
                ->find('auth', 'user_id=:user_id and token=:token and expires_at >= NOW()',
                    ['user_id' => $user_id, 'token' => $token],
                    false);
            if($token_exists_and_matches) {
                // re-login
                App::get('session')->set('user_id', $user_id);
                App::get('session')->set('logged_in', true);
                return null; // authenticated
            } else {
                // remove cookies
                App::get('cookie')->unset('token');
                App::get('cookie')->unset('user_id');
            }
        }
        // fails to authenticate
        return [
            "error" => "Unauthorized",
            "status" => 401,
            "message" => ""
        ];
    }
});
$router->filter('guest', function(){
    if(App::get('session')->isset('logged_in'))
    {
        return [
            "error" => "Not Found",
            "status" => 404,
            "message" => ""
        ];
    }
});
// -----------------------------------------------------------------------
/*
 * Routes Definition - API V1
 */
// -----------------------------------------------------------------------
$router->group(['prefix' => 'api/v1'], function($router){
    /*
     * Posts
     */
    // -----------------------------------------------------------------------
    $router->group(['prefix' => 'posts'], function($router){
        $router->get('', function() {
            return (new PostsController)->index();
        });
        $router->get('{slug:c}', function($id) {
            return (new PostsController)->show($id);
        });
        $router->group(['before' => 'auth'], function($router){
            $router->post('', function() {
                return (new PostsController)->create();
            });
            $router->put('{id:\d+}/update', function($id) {
                return (new PostsController)->update($id);
            });
            $router->delete('{id:\d+}/delete', function($id) {
                return (new PostsController)->delete($id);
            });
            $router->post('generate', function() {
                return (new PostsController)->generate();
            });
        });
    });
    // -----------------------------------------------------------------------
    /*
     * Categories
     */
    // -----------------------------------------------------------------------
    $router->group(['prefix' => 'categories'], function($router){
        $router->get('', function() {
            return (new CategoriesController)->index();
        });
        $router->group(['before' => 'auth'], function($router){
            $router->post('', function() {
                return (new CategoriesController)->create();
            });
            $router->delete('{id:\d+}/delete', function($id) {
                return (new CategoriesController)->delete($id);
            });
        });
    });
    // -----------------------------------------------------------------------
    /*
     * Users
     */
    // -----------------------------------------------------------------------
    $router->group(['prefix' => 'users', 'before' => 'auth'], function($router){
        $router->get('', function() {
            return (new UsersController)->index();
        });
        $router->post('', function() {
            return (new UsersController())->create();
        });
        $router->get('{id:\d+}', function($id) {
            return (new UsersController)->show($id);
        });
        $router->put('{id:\d+}/update', function($id) {
            return (new UsersController)->update($id);
        });
        $router->delete('{id:\d+}/delete', function($id) {
            return (new UsersController)->delete($id);
        });
    });
    // -----------------------------------------------------------------------
    /*
     * Auth
     */
    // -----------------------------------------------------------------------
    $router->group(['prefix' => 'auth'], function($router){
        $router->post('logout', function() {
            return (new AuthController())->logout();
        });

        $router->group(['before' => 'guest'], function($router){
            $router->post('register', function() {
                return (new AuthController())->register();
            });
            $router->post('login', function() {
                return (new AuthController)->login();
            });
        });

    });
    // -----------------------------------------------------------------------
});