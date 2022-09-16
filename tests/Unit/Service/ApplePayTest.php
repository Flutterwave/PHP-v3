<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use Flutterwave\Util\Currency;
use Flutterwave\Helper\Config;

class ApplePayTest extends TestCase
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
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://example.com"
        ];

        $applepayment = \Flutterwave\Flutterwave::create("apple");
        $customerObj = $applepayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $applepayment->payload->create($data);
        $result = $applepayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT, $result['mode']);
    }

    public function testInvalidParams()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://example.com"
        ];

        $applepayment = \Flutterwave\Flutterwave::create("apple");
        //no customer object;
        $payload  = $applepayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $applepayment->initiate($payload);
    }

    public function testEmptyParamsPassed()
    {
        $data = [];
        $applepayment = \Flutterwave\Flutterwave::create("apple");
        $payload  = $applepayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $applepayment->initiate($payload);

    }
}