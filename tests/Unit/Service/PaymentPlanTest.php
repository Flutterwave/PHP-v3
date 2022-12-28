<?php

namespace Unit\Service;

use Flutterwave\Test\Resources\Setup\Config;
use Flutterwave\Payload;
use Flutterwave\Service\PaymentPlan;
use PHPUnit\Framework\TestCase;

class PaymentPlanTest extends TestCase
{

    public PaymentPlan $service;
    protected function setUp(): void
    {
        $this->service = new PaymentPlan(
            Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY], 
                $_SERVER[Config::ENCRYPTION_KEY], 
                $_SERVER[Config::ENV]
            )
        );
    }

    public function testPlanCreation()
    {
        $payload = new Payload();
        $payload->set("amount", "1600");
        $payload->set("name", "PHPSDK Test Plan");
        $payload->set("interval", "monthly");
        $payload->set("duration", "1");


        $request = $this->service->create($payload);

        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));

        return $request->data->id;
    }

    /**
     * @depends testPlanCreation
     */
    public function testRetrievingPlan($id)
    {
        $request = $this->service->get($id);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->id));
    }

    public function testRetrievingPlans()
    {
        $request = $this->service->list();
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    /**
     * @depends testPlanCreation
     */
    public function testUpdatingPlan($id)
    {
        $payload = new Payload();
        $payload->set("amount","600");
        $payload->set("status", "active");
        $request = $this->service->update($id, $payload);
        $this->assertTrue(property_exists($request,'data') && isset($request->data->id));
    }

    /**
     * @depends testPlanCreation
     */
    public function testCancelingPlan($id)
    {
        $request = $this->service->cancel($id);
        $this->assertTrue(property_exists($request,'data') && $request->data->status == "cancelled");
    }
}