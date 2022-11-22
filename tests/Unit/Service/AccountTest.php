<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use Flutterwave\Helper\Config;

class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        \Flutterwave\Flutterwave::bootstrap();
    }

//    public function testAuthModeReturn()
//    {
//        //currently returning "Sorry, we could not connect to your bank";
//
//        $data = [
//            "amount" => 2000,
//            "currency" => Currency::NGN,
//            "tx_ref" => uniqid().time(),
//            "additionalData" => [
//                "account_details" => [
//                    "account_bank" => "044",
//                    "account_number" => "0690000034",
//                    "country" => "NG"
//                ]
//            ],
//        ];
//
//        $accountpayment = \Flutterwave\Flutterwave::create("account");
//        $customerObj = $accountpayment->customer->create([
//            "full_name" => "Temi Adekunle",
//            "email" => "developers@flutterwavego.com",
//            "phone" => "+2349067985861"
//        ]);
//
//        $data['customer'] = $customerObj;
//        $payload  = $accountpayment->payload->create($data);
//        $this->expectException(\Exception::class);
//        $result = $accountpayment->initiate($payload);
//
//        //check mode returned is either OTP or Redirect
////        $this->assertTrue($result['mode'] === AuthMode::OTP || $result['mode'] === AuthMode::REDIRECT );
//    }

//    public function testInvalidParam()
//    {
//        $data = [
//            "amount" => 2000,
//            "currency" => Currency::NGN,
//            "tx_ref" => uniqid().time(),
//            "additionalData" => null,
//        ];
//
//        $accountpayment = \Flutterwave\Flutterwave::create("account");
//        $customerObj = $accountpayment->customer->create([
//            "full_name" => "Jake Jesulayomi Ola",
//            "email" => "developers@flutterwavego.com",
//            "phone" => "+2349067985861"
//        ]);
//
//        $data['customer'] = $customerObj;
//        $payload  = $accountpayment->payload->create($data);
//        $this->expectException(\InvalidArgumentException::class);
//        $result = $accountpayment->initiate($payload);
//    }
}