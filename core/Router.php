<?php

class Router {
    private $routes = [];

    public function add($path, $controller, $method) {
        $this->routes[$path] = [$controller, $method];
    }

    public function dispatch() {
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (isset($this->routes[$requestUri])) {
            list($controller, $method) = $this->routes[$requestUri];
            $controller = "App\\Controllers\\" . $controller;
            
            if (class_exists($controller) && method_exists($controller, $method)) {
                return call_user_func([new $controller(), $method]);
            }
        }

        http_response_code(404);
        echo "404 - Page not found";
    }
}
