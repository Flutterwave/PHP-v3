<?php

declare(strict_types=1);

namespace Flutterwave\Helper;

final class Base
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

    public function isSSL(): bool
    {
        if (isset($_SERVER['HTTPS']) ) {
            if ('on' == strtolower($_SERVER['HTTPS']) ) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS'] ) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
            return true;
        }
        return false;
    }
}
