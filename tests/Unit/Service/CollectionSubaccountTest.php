<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\CollectionSubaccount;
use PHPUnit\Framework\TestCase;

class CollectionSubaccountTest extends TestCase
{
    public function testCollectionSubaccountCreation()
    {
        $payload = new Payload();
        $payload->set("account_bank", "044");
        $payload->set("account_number", "06900000".mt_rand(29, 40));
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount();
        $request = $service->create($payload);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->subaccount_id));
    }

    public function testWhenSubaccountAlreadyExist()
    {
        $payload = new Payload();
        $payload->set("account_bank", "044");
        $payload->set("account_number", "0690000018");
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("A subaccount with the account number and bank already exists");
        $request = $service->create($payload);
    }

    public function testInvalidAccountNumber()
    {
        $payload = new Payload();
        $payload->set("account_bank", "044");
        $payload->set("account_number", "0690000090");
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Sorry we couldn't verify your account number kindly pass a valid account number.");
        $request = $service->create($payload);
    }

    public function testRetrievingCollectionSubaccountList()
    {
        $service = new CollectionSubaccount();
        $request = $service->list();

        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    public function testRetrievingOneSubaccount()
    {
        $service = new CollectionSubaccount();
        $request = $service->get("RS_B7995AEEA79FF3AC16336C53EECB32F0");
        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
    }

    public function testUpdatingCollectionSubaccount()
    {
        $payload = new Payload();
        $payload->set("split_value", "0.2");
        $service = new CollectionSubaccount();
        $request = $service->update("17714", $payload);
        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
    }

    public function testDeletingCollectionSubaccount()
    {
        $service = new CollectionSubaccount();
        $request = $service->delete("17714");
        $this->assertTrue(property_exists($request,'data') && \is_null($request->data));
    }
}