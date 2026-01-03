<?php

namespace Common;

use Closure;

class Router
{
    private array $path;
    private string $method;
    private array $registeredPaths; // Исправлю опечатку :)
    private Closure|array $notFoundHandler;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $trimmedURL = trim(
            parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            "/"
        );
        $this->path = $trimmedURL === '' ? [] : explode("/", $trimmedURL);

        $this->registeredPaths = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => [],
            'PATCH' => []
        ];

        $this->notFoundHandler = function () {
            http_response_code(404);
            echo "<h1>404 - Not Found</h1>";
        };
    }

    public function addPath(string $method, string $path, $handler)
    {
        $method = strtoupper($method);

        if (!isset($this->registeredPaths[$method])) {
            $this->registeredPaths[$method] = [];
        }

        $trimmedURL = trim($path, "/");
        $patternParts = $trimmedURL === '' ? [] : explode("/", $trimmedURL);

        $this->registeredPaths[$method][] = [
            'pattern' => $patternParts,
            'handler' => $handler,
            'source' => $path
        ];
    }

    public function add404(Closure|array $handler)
    {
        $this->notFoundHandler = $handler;
    }

    public function get(string $path, callable|string $handler)
    {
        $this->addPath("GET", $path, $handler);
    }

    public function post(string $path, $handler)
    {
        $this->addPath("POST", $path, $handler);
    }

    public function put(string $path, $handler)
    {
        $this->addPath("PUT", $path, $handler);
    }

    public function delete(string $path, $handler)
    {
        $this->addPath("DELETE", $path, $handler);
    }

    public function patch(string $path, $handler)
    {
        $this->addPath("PATCH", $path, $handler);
    }

    public function run()
    {
        foreach ($this->registeredPaths[$this->method] ?? [] as $route) {
            $params = self::routesIsEqual($this->path, $route['pattern']);

            if ($params !== false) {
                $this->callHandler($route['handler'], $params);
                return;
            }
        }
        $this->callNotFoundHandler();
    }

    private function callHandler($handler, array $params)
    {
        try {
            if (is_string($handler)) {
                if (!str_contains($handler, '@')) {
                    // Просто функция
                    if (!function_exists($handler)) {
                        throw new RouterException("Function $handler not found");
                    }
                    $handler(...$params);
                } else {
                    // Controller@method
                    [$controller, $method] = explode('@', $handler, 2);

                    if (!class_exists($controller)) {
                        throw new RouterException("Controller $controller not found");
                    }

                    if (!method_exists($controller, $method)) {
                        throw new RouterException("Method $method not found in $controller");
                    }

                    $instance = new $controller();
                    $instance->$method(...$params);
                }
            } elseif (is_callable($handler)) {
                $handler(...$params);
            } else {
                throw new RouterException("Invalid handler type");
            }
        } catch (RouterException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo "Internal Server Error";
        }
    }

    private function callNotFoundHandler()
    {
        http_response_code(404);

        if (is_array($this->notFoundHandler)) {
            $handler = $this->notFoundHandler[$this->method]
                ?? $this->notFoundHandler['default']
                ?? current($this->notFoundHandler);

            if (is_callable($handler)) {
                $handler();
            } else {
                echo "<h1>404 - Not Found</h1>";
            }
        } elseif ($this->notFoundHandler instanceof Closure) {
            ($this->notFoundHandler)();
        } else {
            echo "<h1>404 - Not Found</h1>";
        }
    }

    private static function routesIsEqual(array $route, array $pattern): bool|array
    {
        if (count($route) !== count($pattern)) {
            return false;
        }

        $params = [];

        for ($i = 0; $i < count($pattern); $i++) {
            $routePart = $route[$i];
            $patternPart = $pattern[$i];

            if (self::isParameter($patternPart)) {
                $params[] = $routePart;
            } elseif ($routePart !== $patternPart) {
                return false;
            }
        }

        return $params;
    }

    private static function isParameter(string $part): bool
    {
        return strlen($part) >= 3
            && str_starts_with($part, '{')
            && str_ends_with($part, '}');
    }
}

class RouterException extends \Exception {}
