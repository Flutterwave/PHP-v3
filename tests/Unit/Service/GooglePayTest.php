<?php

namespace Unit\Service;

use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class GooglePayTest extends TestCase
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

        $googlepayment = \Flutterwave\Flutterwave::create("google");
        $customerObj = $googlepayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349060085861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $googlepayment->payload->create($data);
        $result = $googlepayment->initiate($payload);

        $this->assertSame(AuthMode::REDIRECT, $result['mode']);
    }
}