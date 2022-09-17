<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Helper\Config;
use Flutterwave\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use Flutterwave\Util\Currency;

class AchTest extends TestCase
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

    public function testAuthModeReturnRedirect()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::ZAR,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://google.com"
        ];

        $achpayment = \Flutterwave\Flutterwave::create("ach");
        $customerObj = $achpayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $achpayment->payload->create($data);

        $result = $achpayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT, $result['mode']);
    }

    public function testBankPermittedToMerchant()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::ZAR,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://google.com"
        ];

        $achpayment = \Flutterwave\Flutterwave::create("ach");
        $customerObj = $achpayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $achpayment->payload->create($data);
        $this->expectExceptionMessage("This bank payment option is not permitted to the merchant");
        $result = $achpayment->initiate($payload);
    }
}