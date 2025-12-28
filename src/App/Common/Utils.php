<?php

namespace Common;

class Utils
{
    public static final string $viewsDir;
    public final string $controllerDir;
    public final string $modelsDir;

    private static function escapeData($value)
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        if (is_array($value)) {
            return array_map('escapeData', $value);
        }

        return $value;
    }

    public static function view(string $name, array $data = [])
    {
        $viewFileName = self::$viewsDir . "$name.php";

        if (!file_exists($viewFileName)) {
            throw new \InvalidArgumentException("Передан несуществующий файл представления");
        }

        $safeData = self::escapeData($data);

        extract($safeData);

        include $viewFileName;
    }
}
