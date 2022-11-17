<?php

namespace Unit\Service;

use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\Bill;

class BillTest extends \PHPUnit\Framework\TestCase
{
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

        $service = new Bill();
        $this->expectException(\InvalidArgumentException::class);
        $request = $service->createPayment($payload);
    }
}