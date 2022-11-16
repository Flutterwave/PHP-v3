<?php

declare(strict_types=1);

namespace Flutterwave\Helper;

class Base
{
    public static function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    public static function getHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getHttpProtocal(): string
    {
        if ($_SERVER['SERVER_NAME'] !== 'localhost') {
            return 'https';
        }
        return 'http';
    }
}
