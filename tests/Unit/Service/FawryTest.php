<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;

class FawryTest extends TestCase
{
    protected function setUp(): void
    {
        \Flutterwave\Flutterwave::bootstrap();
    }

    public function testAuthModeReturnRedirect()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::EGP,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => "https://example.com"
        ];

        $payment = \Flutterwave\Flutterwave::create("fawry");
        $customerObj = $payment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349060085861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $payment->payload->create($data);
        $result = $payment->initiate($payload);

        $this->assertSame('fawry_pay', $result['mode']);
    }

}