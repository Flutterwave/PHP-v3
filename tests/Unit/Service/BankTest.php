<?php

namespace Unit\Service;

use Flutterwave\Service\Banks;
use PHPUnit\Framework\TestCase;
use Flutterwave\Test\Resources\Setup\Config;

class BankTest extends TestCase
{
    public Banks $service;
    protected function setUp(): void
    {
        $this->service = new Banks(
            Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY], 
                $_SERVER[Config::ENCRYPTION_KEY], 
                $_SERVER[Config::ENV]
            )
        );
    }

    public function testRetrievingBankByCountry()
    {
        $response = $this->service->getByCountry("NG");
        $this->assertTrue(property_exists($response,'data') && \is_array($response->data));
    }

    public function testRetrievingBankBranches()
    {
        $response = $this->service->getBranches("280");
        $this->assertTrue(property_exists($response,'data') && \is_array($response->data));
    }
}