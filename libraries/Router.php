<?php

namespace Libraries;

class Router
{
    protected $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[] = compact('method', 'uri', 'action');
    }

    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['uri'] === $requestUri) {
                // $action = [ControllerClass, 'method']
                [$controller, $method] = $route['action'];
                $controllerInstance = new $controller();
                return $controllerInstance->$method();
            }
        }

        // 404 si aucune route ne correspond
        http_response_code(404);
        echo "404 Not Found";
    }
}
