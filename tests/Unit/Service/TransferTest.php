<?php

namespace Unit\Service;

use Flutterwave\Flutterwave;
use Flutterwave\Service\Transfer;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    protected function setUp(): void
    {
        Flutterwave::bootstrap();
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

        $service = new Transfer();
        $customerObj = $service->customer->create([
            "full_name" => "Olaobaju Abraham",
            "email" => "olaobajua@gmail.com",
            "phone" => "+2349067985861"
        ]);
        $data['customer'] = $customerObj;
        $payload  = $service->payload->create($data);
        $response = $service->initiate($payload);

        $this->assertTrue(isset($response['bank_code']) && $response['status'] == "NEW");
    }
}