<?php

declare(strict_types=1);

namespace Flutterwave\Helper;

class CheckCompatibility
{
    public const MINIMUM_COMPATIBILITY = 7.4;
    public static function isCompatible(): bool
    {
        if (Base::getPhpVersion() < self::MINIMUM_COMPATIBILITY) {
            return false;
        }
        return true;
    }

    public function checkExtensions(): bool
    {
        return true;
    }

    public static function isSsl(): bool
    {
        if (isset($_SERVER['HTTPS']) ) {
            if ('on' === strtolower($_SERVER['HTTPS']) ) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS'] ) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ( '443' === $_SERVER['SERVER_PORT'] ) ) {
            return true;
        }
        return false;
    }
}
