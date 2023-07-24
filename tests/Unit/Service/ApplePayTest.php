<?php

namespace Unit\Service;

use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class ApplePayTest extends TestCase
{
    protected function setUp(): void
    {
        \Flutterwave\Flutterwave::bootstrap();
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
            "phone" => "+2349060085861"
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
        $this->expectException(\InvalidArgumentException::class);
        $payload  = $applepayment->payload->create($data);
        $result = $applepayment->initiate($payload);
    }

    public function testEmptyParamsPassed()
    {
        $data = [];
        $applepayment = \Flutterwave\Flutterwave::create("apple");
        $this->expectException(\InvalidArgumentException::class);
        $payload  = $applepayment->payload->create($data);
        $result = $applepayment->initiate($payload);

    }
}