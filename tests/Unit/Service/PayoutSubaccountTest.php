<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Customer;
use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\PayoutSubaccount;
use PHPUnit\Framework\TestCase;

class PayoutSubaccountTest extends TestCase
{
    public function testPayoutSuccountCreation()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $customer = new Customer();
        $customer->set("fullname","Jake Teddy");
        $customer->set("email","jteddy@gmail.com");
        $customer->set("phone_number","+2348065007000");
        $payload = new Payload();
        $payload->set("country", "NG");
        $payload->set("customer", $customer);
        $service = new PayoutSubaccount($config);
        $request = $service->create($payload);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
    }

    public function testRetrievingListOfPayoutSubaccounts()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new PayoutSubaccount($config);
        $request = $service->list();
        print_r($request);
        $this->assertTrue(property_exists($request,'data') && \is_array($request->data));
    }

    public function testRetrievingPayoutSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new PayoutSubaccount($config);
        $request = $service->get("PSA15FAF664D63870782");
        print_r($request);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
    }

    public function testUpdatingPayoutSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("account_name","Aramide Smith");
        $payload->set("mobilenumber","+1409340265");
        $payload->set("email","arasmith676@yahoo.com");
        $payload->set("country","NG");

        $service = new PayoutSubaccount($config);
        $request = $service->update("PSA15FAF664D63870692", $payload);
        print_r($request);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->bank_code));
    }

    public function testFetchingAvailableBalanceOfPayoutSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new PayoutSubaccount($config);
        $request = $service->fetchAvailableBalance("PSA15FAF664D63870692", "USD");
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->available_balance));
    }

    public function testFetchingStaticVirtualAccountOfPayoutSubaccounts()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new PayoutSubaccount($config);
        $request = $service->fetchStaticVirtualAccounts("PSA15FAF664D63870692", "USD");
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->static_account));
    }

    public function testInvalidAccountReference()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("account_name","Aramide Smith");
        $payload->set("mobilenumber","+1409340265");
        $payload->set("email","arasmith676@yahoo.com");
        $payload->set("country","NG");

        $service = new PayoutSubaccount($config);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Account reference is Invalid");
        $request = $service->update("PSA15FAF664D63870782", $payload);
    }
}