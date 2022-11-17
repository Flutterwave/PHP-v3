<?php

namespace Unit\Service;

use Flutterwave\Payload;
use Flutterwave\Service\CollectionSubaccount;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class CollectionSubaccountTest extends TestCase
{
//    public function testCollectionSubaccountCreation()
//    {
//        $payload = new Payload();
//        $payload->set("account_bank", "044");
//        $payload->set("account_number", "06900000" . mt_rand(25, 60));
//        $payload->set("business_name", "Mean Ventures");
//        $payload->set("split_type", "percentage");
//        $payload->set("split_value", "0.5"); // 50%
//        $payload->set("business_mobile", "09087930450");
//        $payload->set("business_email", "developers@flutterwavego.com");
//        $payload->set("country", "NG");
//        $service = new CollectionSubaccount();
//        $request = $service->create($payload);
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->subaccount_id));
//        return $request->data->id;
//    }
//
//    public function testWhenSubaccountAlreadyExist()
//    {
//        $payload = new Payload();
//        $payload->set("account_bank", "044");
//        $payload->set("account_number", "0690000018");
//        $payload->set("business_name", "Mean Ventures");
//        $payload->set("split_type", "percentage");
//        $payload->set("split_value", "0.5"); // 50%
//        $payload->set("business_mobile", "09087930450");
//        $payload->set("business_email", "developers@flutterwavego.com");
//        $payload->set("country", "NG");
//        $service = new CollectionSubaccount();
//        $this->expectException(GuzzleException::class);
//        $this->expectExceptionMessage("A subaccount with the account number and bank already exists");
//        $request = $service->create($payload);
//    }
//
//    public function testInvalidAccountNumber()
//    {
//        $payload = new Payload();
//        $payload->set("account_bank", "044");
//        $payload->set("account_number", "0690000190");
//        $payload->set("business_name", "Maxi Ventures");
//        $payload->set("split_value", "0.5"); // 50%
//        $payload->set("business_mobile", "09087930450");
//        $payload->set("business_email", "vicomma@gmail.com");
//        $payload->set("country", "NG");
//        $service = new CollectionSubaccount();
//        $this->expectException(\Exception::class);
//        $this->expectExceptionMessage("Sorry we couldn't verify your account number kindly pass a valid account number.");
//        $request = $service->create($payload);
//    }
//
//    public function testRetrievingCollectionSubaccountList()
//    {
//        $service = new CollectionSubaccount();
//        $request = $service->list();
//
//        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
//    }
//
//    /**
//     * @depends testCollectionSubaccountCreation
//     */
//    public function testRetrievingOneSubaccount($id)
//    {
//        $service = new CollectionSubaccount();
//        $request = $service->get($id);
//        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
//    }
//
//    /**
//     * @depends testCollectionSubaccountCreation
//     */
//    public function testUpdatingCollectionSubaccount($id)
//    {
//        $payload = new Payload();
//        $payload->set("split_value", "0.2");
//        $service = new CollectionSubaccount();
//        $request = $service->update($id, $payload);
//        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
//    }
//
//    /**
//     * @depends testCollectionSubaccountCreation
//     */
//    public function testDeletingCollectionSubaccount($id)
//    {
//        $service = new CollectionSubaccount();
//        $request = $service->delete($id);
//        $this->assertTrue(property_exists($request,'data') && \is_null($request->data));
//    }
}