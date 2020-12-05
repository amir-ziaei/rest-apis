<?php
/**
 * Router Class
 */

namespace App\Core;

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\{HttpRouteNotFoundException, HttpMethodNotAllowedException};

class Router
{
    protected $router;

    public function load($file)
    {
        $router = new RouteCollector();

        require $file;

        $this->router = $router;

        return $this;
    }

    public function dispatch($method, $uri)
    {
        $dispatcher = new Dispatcher($this->router->getData());
        try {
            $res = $dispatcher->dispatch($method, $uri);
        } catch (HttpRouteNotFoundException $e) {
            (new Response)->e404();
        } catch (HttpMethodNotAllowedException $e) {
            (new Response)->e403();
        }

        (new Response)->send($res);
    }
}
