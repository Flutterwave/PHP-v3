<?php

namespace Unit\Service;

use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\PaymentPlan;
use PHPUnit\Framework\TestCase;

class PaymentPlanTest extends TestCase
{
    public function testPlanCreation()
    {
        \Flutterwave\Flutterwave::bootstrap();

        $payload = new Payload();
        $payload->set("amount", "2000");
        $payload->set("name", "Hulu Extra");
        $payload->set("interval", "monthly");
        $payload->set("duration", "1");

        $service = new PaymentPlan();

        $request = $service->create($payload);

        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlan()
    {
        $service = new PaymentPlan();
        $request = $service->get("15803");
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlans()
    {
        $service = new PaymentPlan();
        $request = $service->list();
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    public function testUpdatingPlan()
    {
        $service = new PaymentPlan();
        $payload = new Payload();
        $payload->set("amount","600");
        $payload->set("status", "active");
        $request = $service->update("15803", $payload);
        $this->assertTrue(property_exists($request,'data') && isset($request->data->id));
    }

    public function testCancelingPlan()
    {
        $service = new PaymentPlan();
        $request = $service->cancel("15803");
        $this->assertTrue(property_exists($request,'data') && $request->data->status == "cancelled");
    }
}