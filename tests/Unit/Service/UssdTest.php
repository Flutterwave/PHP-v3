<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use PHPUnit\Framework\TestCase;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use Flutterwave\Helper\Config;

class UssdTest extends TestCase
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

    public function testAuthModeReturnUssd()
    {
        $data = [
            "amount" => 2000,
            "currency" => Flutterwave\Util\Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "account_bank" => "044"
            ]
        ];

        $ussdpayment = \Flutterwave\Flutterwave::create("ussd");

        $customerObj = $ussdpayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $ussdpayment->payload->create($data);

        $result = $ussdpayment->initiate($payload);

        $this->assertSame(AuthMode::USSD,$result['mode']);
    }
}