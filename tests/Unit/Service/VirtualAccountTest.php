<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\VirtualAccount;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class VirtualAccountTest extends TestCase
{
    public function testVirtualAccountCreation()
    {
        $service = new VirtualAccount();

        $payload = [
            "email" => "kennyio@gmail.com",
            "bvn" => "12345678901",
        ];

        $response = $service->create($payload);
        $this->assertTrue(property_exists(
                $response, "data") && !empty($response->data->order_ref) && isset($response->data->account_number)
        );
    }

    public function testRetrievingBulkVirtualAccounts()
    {
        $service = new VirtualAccount();

        $payload = [
            "accounts" => 5,
            "email" => "kennyio@gmail.com",
            "tx_ref" => "kenny-".time(), // This is a transaction reference that would be returned each time a transfer is done to the account
        ];

        $response = $service->createBulk($payload);
        $this->assertTrue(
            property_exists(
                $response, "data") && !empty($response->data->batch_id) && $response->data->response_code === "02"
        );
    }

    public function testRetrievingVirtualAccount()
    {
        $service = new VirtualAccount();

        $order_ref = "RND_2641579516055928"; // This is the order reference returned on the virtual account number creation

        $response = $service->get($order_ref);

        $this->assertTrue(property_exists(
                $response, "data") && !empty($response->data->account_number) && $response->data->response_code === "02"
        );
    }

    public function testUpdatingVirtualAccount()
    {
        $service = new VirtualAccount();

        $payload = [
            "order_ref" => "RND_2641579516055928",
            "bvn" => "12345678901",
        ];

        $response = $service->update($payload);
        $this->assertTrue(property_exists(
                $response, "data") && $response->status === "success"
        );
    }

    public function testDeletingVirtualAccount()
    {
        $service = new VirtualAccount();

        $order_ref = "RND_2641579516055928"; // This is the order reference returned on the virtual account number creation
        $response = $service->delete($order_ref);

        $this->assertTrue(property_exists(
                $response, "status") && $response->status === "00" && $response->status_desc === "Deactivated successfully"
        );
    }


}
