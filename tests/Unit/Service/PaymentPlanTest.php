<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\PaymentPlan;
use PHPUnit\Framework\TestCase;

class PaymentPlanTest extends TestCase
{
    public function testPlanCreation()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("amount", "2000");
        $payload->set("name", "Hulu Extra");
        $payload->set("interval", "monthly");
        $payload->set("duration", "1");

        $service = new PaymentPlan($config);

        $request = $service->create($payload);

        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlan()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $service = new PaymentPlan($config);
        $request = $service->get("15803");
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlans()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $service = new PaymentPlan($config);
        $request = $service->list();
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    public function testUpdatingPlan()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $service = new PaymentPlan($config);
        $payload = new Payload();
        $payload->set("amount","600");
        $payload->set("status", "active");
        $request = $service->update("15803", $payload);
        $this->assertTrue(property_exists($request,'data') && isset($request->data->id));
    }

    public function testCancelingPlan()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);

        $service = new PaymentPlan($config);
        $request = $service->cancel("15803");
        $this->assertTrue(property_exists($request,'data') && $request->data->status == "cancelled");
    }
}