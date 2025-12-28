<?php

namespace Common;

class Router
{
    private string $action;
    private string $method;
    private array $registredActions;
    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        switch ($this->method) {
            case 'GET':
                $this->action = $_GET["action"] ?? "";
                break;
            case 'POST':
                $this->action = $_POST["action"] ?? "";
                break;
            default:
                break;
        }
        $this->registredActions = [
            "GET" => [],
            "POST" => []
        ];
    }

    public function addAction(string $method, string $action, $handler)
    {
        if (!isset($this->registredActions[$method])) {
            throw new \Exception("Передан неправильный метод", 1);
        }
        if (isset($this->registredActions[$method][$action])) {
            throw new \Exception("Действие $action уже зарегистрировано", 2);
        }
        $this->registredActions[$method][$action] = $handler;
    }

    public function get(string $action, $handler)
    {
        $this->addAction("GET", $action, $handler);
    }
    public function post(string $action, $handler)
    {
        $this->addAction("POST", $action, $handler);
    }

    public function run()
    {
        if (!isset($this->registredActions[$this->method][$this->action])) {
            throw new \Exception("Это действие не зарегистрировано");
        }
        $this->registredActions[$this->method][$this->action]();
    }
}
