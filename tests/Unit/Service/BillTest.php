<?php

namespace Unit\Service;

use Flutterwave\Flutterwave;
use Flutterwave\Payload;
use Flutterwave\Test\Resources\Setup\Config;
use Flutterwave\Service\Bill;

class BillTest extends \PHPUnit\Framework\TestCase
{

    public Bill $service;
    protected function setUp(): void
    {
        $this->service = new Bill(
            Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY], 
                $_SERVER[Config::ENCRYPTION_KEY], 
                $_SERVER[Config::ENV]
            )
        );
    }
//    public function testBillCreation()
//    {
//        $payload = new Payload();
//        $payload->set("country", "NG");
//        $payload->set("customer", "+2349067985861");
//        $payload->set("amount", "2000");
//        $payload->set("type", "AIRTIME");
//        $payload->set("reference", "TEST_".uniqid().uniqid());
//
//        $service = new Bill();
//        $request = $service->createPayment($payload);
//        $this->assertTrue(property_exists($request,'data') && $request->data->flw_ref); //tx_ref not returned on test mode
//    }

    public function testMissingRequiredParam()
    {
        $payload = new Payload();
        $payload->set("country", "NG");
        $payload->set("customer", "+2349067985861");
        $payload->set("amount", "2000");

        $this->expectException(\InvalidArgumentException::class);
        $request = $this->service->createPayment($payload);
    }
}