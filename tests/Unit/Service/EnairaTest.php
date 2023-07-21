<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;

class EnairaTest extends TestCase
{
    protected function setUp(): void
    {
        \Flutterwave\Flutterwave::bootstrap();
    }

    public function testAuthModeReturnRedirect()
    {
        $data = [
            "amount" => 2000,
            "is_token" => 1,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://example.com"
        ];

        $payment = \Flutterwave\Flutterwave::create("enaira");
        $customerObj = $payment->customer->create([
            "full_name" => "Flutterwave Developers",
            "email" => "olaobaju@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $payment->payload->create($data);
        $result = $payment->initiate($payload);
        $this->assertSame(AuthMode::REDIRECT, $result['mode']);
    }
}