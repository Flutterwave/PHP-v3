<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Flutterwave;
use Flutterwave\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use Flutterwave\Util\Currency;
use Flutterwave\Helper\Config;

class MomoTest extends TestCase
{
    protected function setUp(): void
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        Flutterwave::configure($config);
    }

    public function testAuthModeRwandaRedirect(){
        $data = [
            "amount" => 2000,
            "currency" => Currency::RWF,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);

        $result = $momopayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT,$result['mode']);
    }

    public function testAuthModeGhanaRedirect(){
        $data = [
            "amount" => 2000,
            "currency" => Currency::GHS,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);

        $result = $momopayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT,$result['mode']);
    }

    public function testAuthModeUgandaRedirect(){
        $data = [
            "amount" => 2000,
            "currency" => Currency::UGX,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);

        $result = $momopayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT,$result['mode']);
    }

    public function testAuthModeFrancoCallback(){
        $data = [
            "amount" => 2000,
            "currency" => Currency::XAF,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
                "country" => "CM"
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);

        $result = $momopayment->initiate($payload);

        $this->assertSame(AuthMode::CALLBACK,$result['mode']);
    }

    public function testAuthModeZambiaRedirect(){
        $data = [
            "amount" => 2000,
            "currency" => Currency::ZMW,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);

        $result = $momopayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT,$result['mode']);
    }

    public function testInvalidCurrency()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::XOF,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "network" => "MTN",
            ]
        ];

        $momopayment = \Flutterwave\Flutterwave::create("momo");
        $customerObj = $momopayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $momopayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $momopayment->initiate($payload);
    }
}