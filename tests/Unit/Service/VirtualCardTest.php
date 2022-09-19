<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\VirtualCard;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class VirtualCardTest extends TestCase
{
    public function testVirtualCardCreation()
    {
        $payload = new Payload();
        $service = new VirtualCard();

        $payload->set("currency", Currency::NGN);
        $payload->set("amount", "5000");
        $payload->set("debit_currency", Currency::NGN);
        $payload->set("business_mobile", "+234505394568");
        $payload->set("billing_name", "Abraham Smith");
        $payload->set("firstname", "Abraham");
        $response = $service->create($payload);
        $this->assertTrue(property_exists(
            $response, "data") && !empty($response->data->id) && isset($response->data->card_pan)
        );
    }

    public function testRetrievingAllVirtualCards()
    {
        $service = new VirtualCard();
        $request = $service->list();
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    public function testRetrievingVirtualCard()
    {
        $service = new VirtualCard();
        $request = $service->get("213543");
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testVirtualCardFund()
    {
        $data = [
            "amount"=>"3500",
            "debit_currency" => Currency::NGN
        ];
        $service = new VirtualCard();
        $request = $service->fund("213543", $data);
        $this->assertTrue(property_exists($request,'data') && $request->message == "Card funded successfully");
    }

    public function testVirtualCardWithdraw()
    {
        $card_id = "213543";
        $amount = "3500";
        $service = new VirtualCard();
        $request = $service->withdraw($card_id,$amount);
        $this->assertTrue(property_exists($request,'data'));
    }

    public function testVirtualCardBlock()
    {
        $service = new VirtualCard();
        $request = $service->block("213543");
        $this->assertTrue(property_exists($request,'data') && $request->message == "Card blocked successfully");
    }

    public function testVirtualCardTerminate()
    {
        $service = new VirtualCard();
        $request = $service->terminate("213543");
        $this->assertTrue(property_exists($request,'data') && $request->message == "Card terminated successfully");
    }

    public function testRetrievingCardTransactions()
    {
        $data = [
            "from" => "2019-01-01",
            "to" => "2020-01-13",
            "index" => "2",
            "size" => "3"
        ];

        $service = new VirtualCard();
        $request = $service->getTransactions("213543", $data);
        $this->assertTrue(property_exists($request,'data') && $request->message == "Card transactions fetched successfully");
    }
}