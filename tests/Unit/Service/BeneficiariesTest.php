<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\Beneficiaries;
use PHPUnit\Framework\TestCase;

class BeneficiariesTest extends TestCase
{

    public function testBeneficiaryCreation()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("account_bank", "044");
        $payload->set("account_number", "0690000034");
        $payload->set("beneficiary_name", "Abraham Smith Olaolu");
        $service = new Beneficiaries($config);
        $request = $service->create($payload);
        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name == "ACCESS BANK NIGERIA");
    }

    public function testAccountCouldNotBeResolved()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("account_bank", "044");
        $payload->set("account_number", "069000003400234");
        $payload->set("beneficiary_name", "Abraham AB Olaolu");
        $service = new Beneficiaries($config);
        $this->expectException(\Exception::class);
        $request = $service->create($payload);
    }
}