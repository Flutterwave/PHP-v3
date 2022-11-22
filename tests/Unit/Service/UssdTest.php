<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use Flutterwave\Helper\Config;

class UssdTest extends TestCase
{
    protected function setUp(): void
    {
        \Flutterwave\Flutterwave::bootstrap();
    }

//    public function testAuthModeReturnUssd()
//    {
//        $data = [
//            "amount" => 2000,
//            "currency" => Currency::NGN,
//            "tx_ref" => uniqid().time(),
//            "redirectUrl" => null,
//            "additionalData" => [
//                "account_bank" => "044",
//                "account_number" => "000000000000"
//            ]
//        ];
//
//        $ussdpayment = \Flutterwave\Flutterwave::create("ussd");
//
//        $customerObj = $ussdpayment->customer->create([
//            "full_name" => "Olaobaju Jesulayomi Abraham",
//            "email" => "vicomma@gmail.com",
//            "phone" => "+2349067985861"
//        ]);
//
//        $data['customer'] = $customerObj;
//
//        $payload  = $ussdpayment->payload->create($data);
//
//        $result = $ussdpayment->initiate($payload);
//
//        $this->assertSame(AuthMode::USSD,$result['mode']);
//    }

    public function testInvalidArgument()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null
        ];

        $ussdpayment = \Flutterwave\Flutterwave::create("ussd");

        $customerObj = $ussdpayment->customer->create([
            "full_name" => "Olaobaju Jesulayomi Abraham",
            "email" => "vicomma@gmail.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;

        $payload  = $ussdpayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $ussdpayment->initiate($payload);
    }

    public function testInvalidBank()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "redirectUrl" => null,
            "additionalData" => [
                "account_bank" => "204",
                "account_number" => "000000000000"
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
        $this->expectExceptionMessage("USSD Service: We do not support your bank. please kindly use another.");
        $result = $ussdpayment->initiate($payload);
    }
}