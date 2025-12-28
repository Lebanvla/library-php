<?php

namespace Model;

use Common\Database;
use PDO;

class Reader
{
    public static function getList(int $page = 0)
    {
        return Database::query(
            "select * from readers limit 10 offset :offset",
            [
                "value" => $page * 10,
                "type" => PDO::PARAM_INT
            ]
        );
    }
}
