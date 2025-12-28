<?php

namespace Common;

abstract class Controller
{
    protected const VIEWS_DIR = __DIR__ . "/../MVC/views/";

    private static function escapeValue($value)
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        if (is_array($value)) {
            return array_map([self::class, 'escapeValue'], $value);
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return $value;
    }


    protected static function view(string $name, array $data = []): void
    {
        $viewFileName = self::VIEWS_DIR . $name . '.php';

        if (!file_exists($viewFileName)) {
            throw new \InvalidArgumentException(
                "View file not found: {$viewFileName}. " .
                    "Checked path: " . realpath(self::VIEWS_DIR) . "/{$name}.php"
            );
        }

        $safeData = self::escapeValue($data);

        extract($safeData, EXTR_SKIP);

        include $viewFileName;
    }


    protected static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    protected static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
