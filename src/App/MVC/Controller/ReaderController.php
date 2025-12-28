<?php

namespace Controller;

use Common\Controller;
use Model\Reader;

class ReaderController extends Controller
{
    public static function list()
    {
        self::json(
            Reader::getList()
        );
    }
}
