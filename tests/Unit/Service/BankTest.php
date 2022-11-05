<?php

namespace Unit\Service;

use Flutterwave\Service\Banks;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    public function testRetrievingBankByCountry()
    {
        $service = new Banks();
        $response = $service->getByCountry("NG");
        $this->assertTrue(property_exists($response,'data') && \is_array($response->data));
    }

    public function testRetrievingBankBranches()
    {
        $service = new Banks();
        $response = $service->getBranches("280");
        $this->assertTrue(property_exists($response,'data') && \is_array($response->data));
    }
}