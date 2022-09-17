<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\Bill;

class BillTest extends \PHPUnit\Framework\TestCase
{
    public function testBillCreation()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("country", "NG");
        $payload->set("customer", "+2349067985861");
        $payload->set("amount", "2000");
        $payload->set("type", "AIRTIME");
        $payload->set("reference", "TEST_".uniqid().uniqid());

        $service = new Bill($config);
        $request = $service->createPayment($payload);
        $this->assertTrue(property_exists($request,'data') && $request->data->flw_ref); //tx_ref not returned on test mode
    }

    public function testMissingRequiredParam()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("country", "NG");
        $payload->set("customer", "+2349067985861");
        $payload->set("amount", "2000");

        $service = new Bill($config);
        $this->expectException(\InvalidArgumentException::class);
        $request = $service->createPayment($payload);
    }
}