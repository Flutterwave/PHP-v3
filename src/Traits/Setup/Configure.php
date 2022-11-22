<?php

declare(strict_types=1);

namespace Flutterwave\Traits\Setup;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Helper\Config;

trait Configure
{
    public static function bootstrap(?ConfigInterface $config = null): void
    {
        if (\is_null($config)) {
            require __DIR__.'/../../../setup.php';
            $config = Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY],
                $_SERVER[Config::ENCRYPTION_KEY],
                $_SERVER['ENV']
            );
        }
        self::$config = $config;
        self::$methods = require __DIR__ . '/../../Util/methods.php';
    }
}
