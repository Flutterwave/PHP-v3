<?php

namespace Unit\Service;

use Flutterwave\Flutterwave;
use Flutterwave\Service\Transfer;
use Flutterwave\Util\Currency;
use Flutterwave\Test\Resources\Setup\Config;
use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    public Transfer $service;
    protected function setUp(): void
    {
        $this->service = new Transfer(
            Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY], 
                $_SERVER[Config::ENCRYPTION_KEY], 
                $_SERVER[Config::ENV]
            )
        );
    }

    public function testInitiatingTransfer()
    {
        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => "TEST-".uniqid().time()."_PMCKDU_1",
            "redirectUrl" => "https://www.example.com",
            "additionalData" => [
                "account_details" => [
                    "account_bank" => "044",
                    "account_number" => "0690000032",
                    "amount" => "2000",
                    "callback" => null
                ],
                "narration" => "Good Times in the making",
            ],
        ];

        $customerObj = $this->service->customer->create([
            "full_name" => "Olaobaju Abraham",
            "email" => "olaobajua@gmail.com",
            "phone" => "+2349067985861"
        ]);
        $data['customer'] = $customerObj;
        $payload  = $this->service->payload->create($data);
        $response = $this->service->initiate($payload);

        $this->assertTrue(isset($response['bank_code']) && $response['status'] == "NEW");
    }
}