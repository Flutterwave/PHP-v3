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
        $payload = new Payload();
        $payload->set("amount", "1600");
        $payload->set("name", "PHPSDK Test Plan");
        $payload->set("interval", "monthly");
        $payload->set("duration", "1");

        $service = new PaymentPlan();

        $request = $service->create($payload);

        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));

        return $request->data->id;
    }

    /**
     * @depends testPlanCreation
     */
    public function testRetrievingPlan($id)
    {
        $service = new PaymentPlan();
        $request = $service->get($id);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlans()
    {
        $service = new PaymentPlan();
        $request = $service->list();
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    /**
     * @depends testPlanCreation
     */
    public function testUpdatingPlan($id)
    {
        $service = new PaymentPlan();
        $payload = new Payload();
        $payload->set("amount","600");
        $payload->set("status", "active");
        $request = $service->update($id, $payload);
        $this->assertTrue(property_exists($request,'data') && isset($request->data->id));
    }

    /**
     * @depends testPlanCreation
     */
    public function testCancelingPlan($id)
    {
        $service = new PaymentPlan();
        $request = $service->cancel($id);
        $this->assertTrue(property_exists($request,'data') && $request->data->status == "cancelled");
    }
}