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
            (new Responder)->e404();
        } catch (HttpMethodNotAllowedException $e) {
            $allowed_methods = str_replace("Allow: ", '', $e->getMessage());
            (new Responder)->e405($allowed_methods);
        }

        (new Responder)->response($res);
    }
}
