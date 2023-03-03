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
                getenv(Config::SECRET_KEY),
                getenv(Config::PUBLIC_KEY),
                getenv(Config::ENCRYPTION_KEY),
                getenv(Config::ENV)
            );
        }
        self::$config = $config;
        self::$methods = require __DIR__ . '/../../Util/methods.php';
    }
}
