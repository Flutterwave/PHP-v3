<?php

namespace Unit\Service;

use PHPUnit\Framework\TestCase;
use Flutterwave\Flutterwave;
use Flutterwave\Util\AuthMode;
use Flutterwave\Util\Currency;
use Flutterwave\Test\Resources\Setup\Config;

class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        Flutterwave::bootstrap();
    }

    public function testNgnAuthModeReturn()
    {
        //currently returning "Sorry, we could not connect to your bank";

        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => [
                "account_details" => [
                    "account_bank" => "044",
                    "account_number" => "0690000034",
                    "country" => "NG"
                ]
            ],
        ];

        $accountpayment = \Flutterwave\Flutterwave::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Temi Adekunle",
            "email" => "developers@flutterwavego.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $result = $accountpayment->initiate($payload);
        $this->assertTrue( $result['mode'] === AuthMode::REDIRECT );
    }

    public function testInvalidParam()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => null,
        ];

        $accountpayment = \Flutterwave\Flutterwave::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Jake Jesulayomi Ola",
            "email" => "developers@flutterwavego.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $this->expectException(\InvalidArgumentException::class);
        $result = $accountpayment->initiate($payload);
    }

    public function testUKBankAccountAuthMode() {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => uniqid().time(),
            "additionalData" => [
                "account_details" => [
                    "account_bank" => "044",
                    "account_number" => "0690000034",
                    "country" => "UK" //or EU
                ]
            ],
        ];

        $accountpayment = \Flutterwave\Flutterwave::create("account");
        $customerObj = $accountpayment->customer->create([
            "full_name" => "Jake Jesulayomi Ola",
            "email" => "developers@flutterwavego.com",
            "phone" => "+2349067985861"
        ]);

        $data['customer'] = $customerObj;
        $payload  = $accountpayment->payload->create($data);
        $result = $accountpayment->initiate($payload);

        $this->assertTrue( $result['mode'] === AuthMode::REDIRECT );
    }
}