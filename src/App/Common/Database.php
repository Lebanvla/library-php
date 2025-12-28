<?php

namespace Common;

use PDO;
use Common\Config;

class Database
{
    private static ?Self $instance = null;
    private PDO $connection;
    private function __construct()
    {
        $this->connection = new PDO(
            Config::getDbDsn(),
            Config::getDbUser(),
            Config::getDbPassword(),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function __wakeup()
    {
        throw new \Exception('Not implemented');
    }

    public function __clone()
    {
        throw new \Exception('Not implemented');
    }

    public static function getInstance(): Self
    {
        if (self::$instance === null) {
            self::$instance = new Self();
        }
        return self::$instance;
    }

    public static function getConnection(): PDO
    {
        return self::getInstance()->connection;
    }

    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getConnection()->prepare($sql);

        foreach ($params as $paramName => $paramData) {  // $paramName = ':offset'
            $stmt->bindValue($paramName, $paramData["value"], $paramData["type"]);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
