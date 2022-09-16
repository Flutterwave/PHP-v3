<?php

namespace Flutterwave\Traits\Setup;

use Flutterwave\Helper\Config;

trait Configure
{
    public  static function configure(Config $config)
    {
        self::$methods = require __DIR__ . "/../../Util/methods.php"; //TODO: update the methods
        self::$config = $config;
    }
}