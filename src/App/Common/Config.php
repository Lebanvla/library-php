<?php

namespace Common;

class Config
{
    private static array $configData = [];

    public static function initialize(): void
    {
        self::$configData = [
            'APP_ENV' => getenv('APP_ENV'),
            'APP_HOST' => getenv('DB_HOST'),
            'DB_NAME' => getenv('DB_NAME'),
            'DB_USERNAME' => getenv('DB_USERNAME'),
            'DB_PASSWORD' => getenv('DB_PASSWORD'),
            'APACHE_PORT' => getenv('APACHE_PORT'),
            'MYSQL_PORT' => getenv('MYSQL_PORT'),
            'DB_DATABASE' => getenv('DB_DATABASE'),
            'DB_HOST' => getenv('DB_HOST')
        ];


        $required = ['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'APP_ENV', 'DB_HOST'];
        foreach ($required as $key) {
            if (!isset(self::$configData[$key])) {
                throw new \RuntimeException("Missing required ENV variable: {$key}");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (empty(self::$configData)) {
            self::initialize();
        }
        return self::$configData[$key] ?? $default;
    }

    public static function getDbName(): string
    {
        return self::get('DB_DATABASE', 'library');
    }

    public static function getDbUser(): string
    {
        return self::get('DB_USERNAME', 'lebanvla');
    }

    public static function getDbPassword(): string
    {
        return self::get('DB_PASSWORD', '');
    }

    public static function getDbDsn(): string
    {
        return sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4;port=3306',
            self::getAppHost(),
            self::getDbName()
        );
    }

    public static function getDBHost(): string
    {
        return self::$configData['DB_HOST'];
    }

    public static function getAppHost(): string
    {
        return self::get('APP_HOST', 'localhost');
    }

    public static function getAppPort(): string
    {
        return self::get('APACHE_PORT', '8080');
    }

    public static function getAppUrl(): string
    {
        return sprintf(
            'http://%s:%s',
            self::getAppHost(),
            self::getAppPort()
        );
    }

    public static function getMode(): string
    {
        return self::$configData["APP_ENV"];
    }
}
