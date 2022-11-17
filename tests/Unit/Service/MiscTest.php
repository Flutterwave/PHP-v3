<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\Misc;
use Flutterwave\Util\Currency;
use PHPUnit\Framework\TestCase;

class MiscTest extends TestCase
{
//    public function testRetrievingOneWallet()
//    {
//        $service = new Misc();
//        $response = $service->getWallet(Currency::NGN);
//        $this->assertTrue(
//            property_exists($response, "data") && !empty($response->data->available_balance)
//        );
//    }
//
//    public function testRetrievingAllWallets()
//    {
//        $service = new Misc();
//        $response = $service->getWallets();
//        $this->assertTrue(property_exists($response, "data") && \is_array($response->data));
//
//    }
//
//    public function testRetrievingBalanceHistory()
//    {
//        $service = new Misc();
//        $data = [
//            "from" => "2020-05-15",
//            "to" => "2020-09-10",
//            "currency" => Currency::NGN,
//        ];
//
//        $response = $service->getBalanceHistory($data);
//        $this->assertTrue(
//            property_exists($response, "data") && \is_array($response->data->transactions)
//        );
//    }
//
//    public function testResolvingAccount()
//    {
//        $payload = new Payload();
//        $service = new Misc();
//
//        $payload->set("account_number","0690000033");
//        $payload->set("account_bank","044");
//        $response = $service->resolveAccount($payload);
//        $this->assertTrue(
//            property_exists($response, "data") && !empty($response->data->account_number)
//        );
//    }

//    public function testResolvingBvn()
//    {
//        $service = new Misc();
//        $response = $service->resolveBvn("203004042344532");
//        $this->assertTrue(
//            property_exists($response, "data") && isset($response->data->first_name)
//            && isset($response->data->middle_name) && isset($response->data->last_name)
//        );
//    }

//    public function testResolvingCardBin()
//    {
//        $service = new Misc();
//        $response = $service->resolveCardBin("539983");
//        $this->assertTrue(
//            property_exists($response, "data") && !empty($response->data->issuing_country)
//            && $response->data->card_type == "MASTERCARD"
//        );
//    }
}