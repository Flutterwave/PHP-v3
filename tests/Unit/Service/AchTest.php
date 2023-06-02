<?php

namespace Unit\Service;

use Flutterwave\Util\AuthMode;
use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\Util\Currency;
use Flutterwave\Test\Resources\Setup\Config;


class AchTest extends TestCase
{
    protected function setUp(): void
    {
        Flutterwave::bootstrap();
    }

//    public function testAuthModeReturnRedirect()
//    {
//        $data = [
//            "amount" => 2000,
//            "currency" => Currency::ZAR,
//            "tx_ref" => uniqid().time(),
//            "redirectUrl" => "https://google.com"
//        ];
//
//        $achpayment = Flutterwave::create("ach");
//        $customerObj = $achpayment->customer->create([
//            "full_name" => "Olaobaju Jesulayomi Abraham",
//            "email" => "vicomma@gmail.com",
//            "phone" => "+2349067985861"
//        ]);
//
//        $data['customer'] = $customerObj;
//        $payload  = $achpayment->payload->create($data);
//
//        $result = $achpayment->initiate($payload);
//
//        $this->assertSame(AuthMode::REDIRECT, $result['mode']);
//    }

    // public function testBankPermittedToMerchant()
    // {
    //     $data = [
    //         "amount" => 2000,
    //         "currency" => Currency::ZAR,
    //         "tx_ref" => uniqid().time(),
    //         "redirectUrl" => "https://google.com"
    //     ];

    //     $achpayment = Flutterwave::create("ach");
    //     $customerObj = $achpayment->customer->create([
    //         "full_name" => "Olaobaju Jesulayomi Abraham",
    //         "email" => "vicomma@gmail.com",
    //         "phone" => "+2349067985861"
    //     ]);

    //     $data['customer'] = $customerObj;
    //     $payload  = $achpayment->payload->create($data);
    //     $this->expectExceptionMessage("This bank payment option is not permitted to the merchant");
    //     $result = $achpayment->initiate($payload);
    // }
}