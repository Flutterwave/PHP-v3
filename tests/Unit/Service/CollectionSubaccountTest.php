<?php

namespace Unit\Service;

require __DIR__.'/../../../setup.php';

use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Service\CollectionSubaccount;
use PHPUnit\Framework\TestCase;

class CollectionSubaccountTest extends TestCase
{
    protected function setUp(): void
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );
        \Flutterwave\Flutterwave::configure($config);
    }

    public function testCollectionSubaccountCreation()
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
        $payload->set("account_number", "06900000".mt_rand(29, 40));
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount($config);
        $request = $service->create($payload);
        print_r($request);
        $this->assertTrue(property_exists($request,'data') && !empty($request->data->subaccount_id));
    }

    public function testWhenSubaccountAlreadyExist()
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
        $payload->set("account_number", "0690000018");
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount($config);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("A subaccount with the account number and bank already exists");
        $request = $service->create($payload);
    }

    public function testInvalidAccountNumber()
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
        $payload->set("account_number", "0690000090");
        $payload->set("business_name", "Maxi Ventures");
        $payload->set("split_value", "0.5"); // 50%
        $payload->set("business_mobile", "09087930450");
        $payload->set("business_email", "vicomma@gmail.com");
        $payload->set("country", "NG");
        $service = new CollectionSubaccount($config);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Sorry we couldn't verify your account number kindly pass a valid account number.");
        $request = $service->create($payload);
    }

    public function testRetrievingCollectionSubaccountList()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new CollectionSubaccount($config);
        $request = $service->list();

        $this->assertTrue(property_exists($request,'data') && is_array($request->data));
    }

    public function testRetrievingOneSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new CollectionSubaccount($config);
        $request = $service->get("RS_B7995AEEA79FF3AC16336C53EECB32F0");
        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
    }

    public function testUpdatingCollectionSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $payload = new Payload();
        $payload->set("split_value", "0.2");
        $service = new CollectionSubaccount($config);
        $request = $service->update("17714", $payload);
        $this->assertTrue(property_exists($request,'data') && $request->data->bank_name = "ACCESS BANK NIGERIA");
    }

    public function testDeletingCollectionSubaccount()
    {
        $config = Config::getInstance(
            $_SERVER[Config::SECRET_KEY],
            $_SERVER[Config::PUBLIC_KEY],
            $_SERVER[Config::ENCRYPTION_KEY],
            $_SERVER['ENV']
        );

        Flutterwave::configure($config);

        $service = new CollectionSubaccount($config);
        $request = $service->delete("17714");
        $this->assertTrue(property_exists($request,'data') && \is_null($request->data));
    }
}