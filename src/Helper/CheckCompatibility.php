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
}
