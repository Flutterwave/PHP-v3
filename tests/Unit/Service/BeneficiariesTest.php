<?php

namespace Unit\Service;

use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\Beneficiaries;
use PHPUnit\Framework\TestCase;

class BeneficiariesTest extends TestCase
{

//    public function testBeneficiaryCreation()
//    {
//        $payload = new Payload();
//        $payload->set("account_bank", "044");
//        $payload->set("account_number", "0690000033");
//        $payload->set("beneficiary_name", "Abraham Smith Olaolu");
//        $service = new Beneficiaries();
//        $request = $service->create($payload);
//        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name == "ACCESS BANK NIGERIA");
//    }

//    public function testAccountCouldNotBeResolved()
//    {
//        $payload = new Payload();
//        $payload->set("account_bank", "044");
//        $payload->set("account_number", "069000003400234");
//        $payload->set("beneficiary_name", "Abraham AB Olaolu");
//        $service = new Beneficiaries();
//        $this->expectException(\Exception::class);
//        $request = $service->create($payload);
//    }
}