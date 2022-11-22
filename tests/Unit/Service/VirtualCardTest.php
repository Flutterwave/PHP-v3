<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\VirtualCard;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class VirtualCardTest extends TestCase
{
//    public function testVirtualCardCreation()
//    {
//        $payload = new Payload();
//        $service = new VirtualCard();
//
//        $payload->set("first_name","PHP");
//        $payload->set("last_name","SDK");
//        $payload->set("date_of_birth","1994-03-01");
//        $payload->set("title","Mr");
//        $payload->set("gender","M"); //M or F
//        $payload->set("email","developers@flutterwavego.com");
//        $payload->set("currency", Currency::NGN);
//        $payload->set("amount", "5000");
//        $payload->set("debit_currency", Currency::NGN);
//        $payload->set("phone", "+234505394568");
//        $payload->set("billing_name", "Abraham Ola");
//        $payload->set("firstname", "Abraham");
//        $response = $service->create($payload);
//        $this->assertTrue(property_exists(
//            $response, "data") && !empty($response->data->id) && isset($response->data->card_pan)
//        );
//
//        return $response->data->id;
//    }
//
//    public function testRetrievingAllVirtualCards()
//    {
//        $service = new VirtualCard();
//        $request = $service->list();
//        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
//    }
//
//    /**
//     * @depends testVirtualCardCreation
//     */
//    public function testRetrievingVirtualCard(string $id)
//    {
//        $service = new VirtualCard();
//        $request = $service->get($id);
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
//    }
//
//
//    /**
//     * @depends testVirtualCardCreation
//     */
//    public function testVirtualCardFund(string $id)
//    {
//        $data = [
//            "amount"=>"3500",
//            "debit_currency" => Currency::NGN
//        ];
//        $service = new VirtualCard();
//        $request = $service->fund($id, $data);
//        $this->assertTrue(property_exists($request,'data') && $request->message == "Card funded successfully");
//    }
//
//    /**
//     * @depends testVirtualCardCreation
//     */
//    public function testVirtualCardWithdraw(string $id)
//    {
//        $card_id = $id;
//        $amount = "3500";
//        $service = new VirtualCard();
//        $request = $service->withdraw($card_id,$amount);
//        $this->assertTrue(property_exists($request,'data'));
//    }
//
////    /**
////     * @depends testVirtualCardCreation
////     */
////    public function testVirtualCardBlock(string $id)
////    {
////        $service = new VirtualCard();
////        $request = $service->block($id);
////        $this->assertTrue(property_exists($request,'data') && $request->message == "Card blocked successfully");
////    }
//
//    /**
//     * @depends testVirtualCardCreation
//     */
//    public function testVirtualCardTerminate(string $id)
//    {
//        $service = new VirtualCard();
//        $request = $service->terminate($id);
//        $this->assertTrue(property_exists($request,'data') && $request->message == "Card terminated successfully");
//    }
//
//    /**
//     * @depends testVirtualCardCreation
//     */
//    public function testRetrievingCardTransactions(string $id)
//    {
//        $data = [
//            "from" => "2019-01-01",
//            "to" => "2020-01-13",
//            "index" => "2",
//            "size" => "3"
//        ];
//
//        $service = new VirtualCard();
//        $request = $service->getTransactions($id, $data);
//        $this->assertTrue(property_exists($request,'data') && $request->message == "Card transactions fetched successfully");
//    }
}