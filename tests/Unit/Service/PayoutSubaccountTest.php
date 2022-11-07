<?php

namespace Unit\Service;

use Flutterwave\Customer;
use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\PayoutSubaccount;
use PHPUnit\Framework\TestCase;

class PayoutSubaccountTest extends TestCase
{
//    public function testPayoutSuccountCreation()
//    {
//        $customer = new Customer();
//        $customer->set("fullname","PHP Person");
//        $customer->set("email","cornelius@flutterwavego.com");
//        $customer->set("phone_number","+2348065007910");
//        $payload = new Payload();
//        $payload->set("country", "NG");
//        $payload->set("customer", $customer);
//        $service = new PayoutSubaccount();
//        $request = $service->create($payload);
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
//
//        return $request->data->account_reference;
//    }
//
//    public function testRetrievingListOfPayoutSubaccounts()
//    {
//        $service = new PayoutSubaccount();
//        $request = $service->list();
//        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
//    }
//
//    /**
//     * @depends testPayoutSuccountCreation
//     */
//    public function testRetrievingPayoutSubaccount($account_reference)
//    {
//        $service = new PayoutSubaccount();
//        $request = $service->get($account_reference);
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
//    }
//
//    /**
//     * @depends testPayoutSuccountCreation
//     */
//    public function testUpdatingPayoutSubaccount($account_reference)
//    {
//        $payload = new Payload();
//        $payload->set("account_name","Aramide Smith");
//        $payload->set("mobilenumber","+2348065007080");
//        $payload->set("email","developers@flutterwavego.com");
//        $payload->set("country","NG");
//
//        $service = new PayoutSubaccount();
//        $request = $service->update($account_reference, $payload);
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
//    }
//
//    /**
//     * @depends testPayoutSuccountCreation
//     */
//    public function testFetchingAvailableBalanceOfPayoutSubaccount($account_reference)
//    {
//        $service = new PayoutSubaccount();
//        $request = $service->fetchAvailableBalance($account_reference, "USD");
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->available_balance));
//    }
//
//    /**
//     * @depends testPayoutSuccountCreation
//     */
//    public function testFetchingStaticVirtualAccountOfPayoutSubaccounts($account_reference)
//    {
//        $service = new PayoutSubaccount();
//        $request = $service->fetchStaticVirtualAccounts($account_reference, "USD");
//        $this->assertTrue(property_exists($request,'data') && !empty($request->data->static_account));
//    }

//    public function testInvalidAccountReference()
//    {
//        $payload = new Payload();
//        $payload->set("account_name","Aramide Smith");
//        $payload->set("mobilenumber","+1409340265");
//        $payload->set("email","arasmith676@yahoo.com");
//        $payload->set("country","NG");
//
//        $service = new PayoutSubaccount();
//        $this->expectException(\Exception::class);
//        $this->expectExceptionMessage("Account reference is Invalid");
//        $request = $service->update("PSA15FAF664D63870782", $payload);
//    }
}