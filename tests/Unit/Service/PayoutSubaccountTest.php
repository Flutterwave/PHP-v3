<?php

namespace Unit\Service;

use Flutterwave\Helper\Config;
use PHPUnit\Framework\TestCase;

class PayoutSubaccountTest extends TestCase
{
    protected function setUp(): void
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);
    }
}