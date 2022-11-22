<p align="center">
    <img title="Flutterwave" height="200" src="https://flutterwave.com/images/logo/full.svg" width="50%"/>
</p>

# Flutterwave v3 PHP SDK.

![Packagist Downloads](https://img.shields.io/packagist/dt/flutterwavedev/flutterwave-v3)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/flutterwavedev/flutterwave-v3)
![GitHub stars](https://img.shields.io/github/stars/Flutterwave/Flutterwave-PHP-v3)
![Packagist License](https://img.shields.io/packagist/l/flutterwavedev/flutterwave-v3)

This Flutterwave v3 PHP Library provides easy access to Flutterwave for Business (F4B) v3 APIs from php apps. It abstracts the complexity involved in direct integration and allows you to make quick calls to the APIs.

Available features include:

- Collections: Card, Account, Mobile money, Bank Transfers, USSD, Barter, NQR.
- Payouts and Beneficiaries.
- Recurring payments: Tokenization and Subscriptions.
- Split payments
- Card issuing
- Transactions dispute management: Refunds.
- Transaction reporting: Collections, Payouts, Settlements, and Refunds.
- Bill payments: Airtime, Data bundle, Cable, Power, Toll, E-bills, and Remitta.
- Identity verification: Resolve bank account, resolve BVN information.

## Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Initialization](#initialization)
4. [Usage](#usage)
5. [Testing](#testing)
6. [Debugging Errors](#debugging-errors)
7. [Support](#support)
8. [Contribution guidelines](#contribution-guidelines)
9. [License](#license)
10. [Changelog](#changelog)

<a id="requirements"></a>

## Requirements

1. Flutterwave for business [API Keys](https://developer.flutterwave.com/docs/integration-guides/authentication)
2. Acceptable PHP versions: >= 5.4.0


<a id="installation"></a>

## Installation

The vendor folder is committed into the project to allow easy installation for those who do not have composer installed.
It is recommended to update the project dependencies using:

```shell
$ composer require flutterwavedev/flutterwave-v3
```

<a id="initialization"></a>

## Initialization

Create a .env file and follow the format of the .env.example file
Save your PUBLIC_KEY, SECRET_KEY, ENV in the .env file

```env

PUBLIC_KEY="****YOUR**PUBLIC**KEY****" // can be gotten from the dashboard
SECRET_KEY="****YOUR**SECRET**KEY****" // can be gotten from the dashboard
ENCRYPTION_KEY="Encryption key"
ENV="development/production"

```


<a id="usage"></a>

## Usage

### Card Charge
This is used to facilitate card transactions.

Edit the `paymentForm.php` and `processPayment.php` files to suit your purpose. Both files are well documented.

Simply redirect to the `paymentForm.php` file on your browser to process a payment.

In this implementation, we are expecting a form encoded POST request to this script.
The request will contain the following parameters.

- payment_method `Can be card, account, both`
- description `Your transaction description`
- logo `Your logo url`
- title `Your transaction title`
- country `Your transaction country`
- currency `Your transaction currency`
- email `Your customer's email`
- firstname `Your customer's first name`
- lastname `Your customer's last name`
- phonenumber `Your customer's phonenumber`
- pay_button_text `The payment button text you prefer`
- ref `Your transaction reference. It must be unique per transaction.  By default, the Rave class generates a unique transaction reference for each transaction. Pass this parameter only if you uncommented the related section in the script below.`

```php
<?php

require __DIR__."/vendor/autoload.php";

session_start();

const BASEPATH = 1;

use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Flutterwave;

\Flutterwave\Flutterwave::bootstrap();

$URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$getData = $_GET;
$postData = $_POST;
$publicKey = $_SERVER['PUBLIC_KEY'];
$secretKey = $_SERVER['SECRET_KEY'];
if (isset($_POST) && isset($postData['successurl']) && isset($postData['failureurl'])) {
    $success_url = $postData['successurl'];
    $failure_url = $postData['failureurl'];
}

$env = $_SERVER['ENV'];

if (isset($postData['amount'])) {
    $_SESSION['publicKey'] = $publicKey;
    $_SESSION['secretKey'] = $secretKey;
    $_SESSION['env'] = $env;
    $_SESSION['successurl'] = $success_url;
    $_SESSION['failureurl'] = $failure_url;
    $_SESSION['currency'] = $postData['currency'];
    $_SESSION['amount'] = $postData['amount'];
}

$prefix = 'RV'; // Change this to the name of your business or app
$overrideRef = false;

// Uncomment here to enforce the useage of your own ref else a ref will be generated for you automatically
if (isset($postData['ref'])) {
    $prefix = $postData['ref'];
    $overrideRef = true;
}

$payment = new Flutterwave($prefix, $overrideRef);

function getURL($url, $data = array()) {
    $urlArr = explode('?', $url);
    $params = array_merge($_GET, $data);
    $new_query_string = http_build_query($params) . '&' . $urlArr[1];
    $newUrl = $urlArr[0] . '?' . $new_query_string;
    return $newUrl;
}

```

In order to handle events that at occurs at different transaction stages. You define a class that implements the ```EventHandlerInterface```

```php
// This is where you set how you want to handle the transaction at different stages
class myEventHandler implements EventHandlerInterface
{
    /**
     * This is called when the Rave class is initialized
     * */
    function onInit($initializationData) {
        // Save the transaction to your DB.
    }

    /**
     * This is called only when a transaction is successful
     * */
    function onSuccessful($transactionData) {
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
        if ($transactionData->status === 'successful') {
            if ($transactionData->currency == $_SESSION['currency'] && $transactionData->amount == $_SESSION['amount']) {

                if ($_SESSION['publicKey']) {
                    header('Location: ' . getURL($_SESSION['successurl'], array('event' => 'successful')));
                    $_SESSION = array();
                    session_destroy();
                }
            } else {
                if ($_SESSION['publicKey']) {
                    header('Location: ' . getURL($_SESSION['failureurl'], array('event' => 'suspicious')));
                    $_SESSION = array();
                    session_destroy();
                }
            }
        } else {
            $this->onFailure($transactionData);
        }
    }

    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData) {
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
        if ($_SESSION['publicKey']) {
            header('Location: ' . getURL($_SESSION['failureurl'], array('event' => 'failed')));
            $_SESSION = array();
            session_destroy();
        }
    }

    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference) {
        // Do something, anything!
    }

    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse) {
        echo 'the transaction was not found';
    }

    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference) {
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
        if ($_SESSION['publicKey']) {
            header('Location: ' . getURL($_SESSION['failureurl'], array('event' => 'canceled')));
            $_SESSION = array();
            session_destroy();
        }
    }

    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data) {
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
        if ($_SESSION['publicKey']) {
            header('Location: ' . getURL($_SESSION['failureurl'], array('event' => 'timedout')));
            $_SESSION = array();
            session_destroy();
        }
    }
}

if (isset($postData['amount'])) {
    // Make payment
    $payment
        ->eventHandler(new myEventHandler)
        ->setAmount($postData['amount'])
        ->setPaymentOptions($postData['payment_options']) // value can be card, account or both
        ->setDescription($postData['description'])
        ->setLogo($postData['logo'])
        ->setTitle($postData['title'])
        ->setCountry($postData['country'])
        ->setCurrency($postData['currency'])
        ->setEmail($postData['email'])
        ->setFirstname($postData['firstname'])
        ->setLastname($postData['lastname'])
        ->setPhoneNumber($postData['phonenumber'])
        ->setPayButtonText($postData['pay_button_text'])
        ->setRedirectUrl($URL)
        // ->setMetaData(array('metaname' => 'SomeDataName', 'metavalue' => 'SomeValue')) // can be called multiple times. Uncomment this to add meta datas
        // ->setMetaData(array('metaname' => 'SomeOtherDataName', 'metavalue' => 'SomeOtherValue')) // can be called multiple times. Uncomment this to add meta datas
        ->initialize();
} else {
    if (isset($getData['cancelled'])) {
        // Handle canceled payments
        $payment
            ->eventHandler(new myEventHandler)
            ->paymentCanceled($getData['cancel_ref']);
    } elseif (isset($getData['tx_ref'])) {
        // Handle completed payments
        $payment->logger->notice('Payment completed. Now requerying payment.');
        $payment
            ->eventHandler(new myEventHandler)
            ->requeryTransaction($getData['transaction_id']);
    } else {
        $payment->logger->warning('Stop!!! Please pass the txref parameter!');
        echo 'Stop!!! Please pass the txref parameter!';
    }
}
```
<br>

### Configuration settings
Create a .env file and add the bootstrap method first before initiating a charge.
```php
use \Flutterwave\Flutterwave;
# normal configuration
Flutterwave::bootstrap();

# for a custom configuration
# your config must implement Flutterwave\Contract\ConfigInterface 
Flutterwave::bootstrap($myConfig);
```

### Account Charge

The following implementation shows how to initiate a direct bank charge. <br /> 
want to see it work real time? a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/account.php).

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => uniqid().time(),
    "additionalData" => [
        "account_details" => [
            "account_bank" => "044",
            "account_number" => "0690000034",
            "country" => "NG"
        ]
    ],
];

$accountpayment = \Flutterwave\Flutterwave::create("account");
$customerObj = $accountpayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349067985861"
]);

$data['customer'] = $customerObj;
$payload  = $accountpayment->payload->create($data);
$result = $accountpayment->initiate($payload);
```
<br>

### ACH Charge

The following implementation shows how to accept payments directly from customers in the US and South Africa. a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/ach.php).

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::ZAR,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => "https://google.com"
];

$achpayment = \Flutterwave\Flutterwave::create("ach");
$customerObj = $achpayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349067985861"
]);

$data['customer'] = $customerObj;
$payload  = $achpayment->payload->create($data);

$result = $achpayment->initiate($payload);
```

<br>

### Direct Card Charge

The following implementation shows how to initiate a card charge. a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/card.php)

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => "TEST-".uniqid().time(),
    "redirectUrl" => "https://www.example.com",
    "additionalData" => [
        "subaccounts" => [
            ["id" => "RSA_345983858845935893"]
        ],
        "meta" => [
            "unique_id" => uniqid().uniqid()
        ],
        "preauthorize" => false,
        "payment_plan" => null,
        "card_details" => [
            "card_number" => "5531886652142950",
            "cvv" => "564",
            "expiry_month" => "09",
            "expiry_year" => "32"
        ]
    ],
];

$cardpayment = \Flutterwave\Flutterwave::create("card");
$customerObj = $cardpayment->customer->create([
    "full_name" => "Olaobaju Abraham",
    "email" => "olaobajua@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$payload  = $cardpayment->payload->create($data);
$result = $cardpayment->initiate($payload);
```

### Mobile Money Payments

The following implementation shows how to initiate a mobile money payment. a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/momo.php).

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::XOF,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => null,
    "additionalData" => [
        "network" => "MTN",
    ]
];

$momopayment = \Flutterwave\Flutterwave::create("momo");
$customerObj = $momopayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$payload  = $momopayment->payload->create($data);
$result = $momopayment->initiate($payload);
```

### USSD

Collect payments via ussd. a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/ussd.php)

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => null,
    "additionalData" => [
        "account_bank" => "044",
        "account_number" => "000000000000"
    ]
];

$ussdpayment = \Flutterwave\Flutterwave::create("ussd");
$customerObj = $ussdpayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$payload  = $ussdpayment->payload->create($data);
$result = $ussdpayment->initiate($payload);
```

<br>

### Mpesa

Collect payments from your customers via Mpesa.a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/mpesa.php)

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => "https://google.com"
];

$mpesapayment = \Flutterwave\Flutterwave::create("mpesa");
$customerObj = $mpesapayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$payload  = $mpesapayment->payload->create($data);
$result = $mpesapayment->initiate($payload);
```

### Transfer Implementation

How to make a transfer payment

```php
$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => "TEST-".uniqid().time()."_PMCKDU_1",
    "redirectUrl" => "https://www.example.com",
    "additionalData" => [
        "account_details" => [
            "account_bank" => "044",
            "account_number" => "0690000032",
            "amount" => "2000",
            "callback" => null
        ],
        "narration" => "Good Times in the making",
    ],
];

$service = new Transfer();
$customerObj = $service->customer->create([
    "full_name" => "Olaobaju Abraham",
    "email" => "olaobajua@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$payload  = $service->payload->create($data);
$response = $service->initiate($payload);
```

<br>

### Virtual Card

The following implementation shows how to create virtual cards on rave. Use the Playground Directory to view Responses and samples of use.

```php
use Flutterwave\Payload;
use Flutterwave\Service\VirtualCard;
use Flutterwave\Util\Currency;

$payload = new Payload();
$service = new VirtualCard();

$payload->set("currency", Currency::NGN);
$payload->set("amount", "5000");
$payload->set("debit_currency", Currency::NGN);
$payload->set("business_mobile", "+234505394568");
$payload->set("billing_name", "Abraham Smith");
$payload->set("firstname", "Abraham");
$response = $service->create($payload);
```

### BVN Verification

The following implementation shows how to verify a Bank Verification Number.

```php
use Flutterwave\Service\Misc;

$service = new Misc();
$response = $service->resolveBvn("203004042344532");
```

<br>

### Payment Plans

The following implementation shows how to create a payment plan on the rave dashboard. Use the Playground Directory to view Responses and samples of use.

```php
use Flutterwave\Payload;
use Flutterwave\Service\PaymentPlan;

$payload = new Payload();
$payload->set("amount", "2000");
$payload->set("name", "Hulu Extra");
$payload->set("interval", "monthly");
$payload->set("duration", "1");

$service = new PaymentPlan($config);
$request = $service->create($payload);
```

<br>

### Collection Subaccount

The following implementation shows how to create a subaccount via PHP SDK.

```php
use Flutterwave\Payload;
use Flutterwave\Service\CollectionSubaccount;

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
```

### Payout Subaccount

The following implementation shows how to create a payout subaccount via PHP SDK.

```php
use Flutterwave\Payload;
use Flutterwave\Customer;
use Flutterwave\Service\PayoutSubaccount;

$customer = new Customer();
$customer->set("fullname","Jake Teddy");
$customer->set("email","jteddy@gmail.com");
$customer->set("phone_number","+2348065007000");
$payload = new Payload();
$payload->set("country", "NG");
$payload->set("customer", $customer);
$service = new PayoutSubaccount($config);
$request = $service->create($payload);
```

<br>

### Beneficiaries

The following implementation shows how to create a transfer Beneficiary via the PHP SDK.

```php
use Flutterwave\Payload;
use Flutterwave\Service\Beneficiaries;

$payload = new Payload();
$payload->set("account_bank", "044");
$payload->set("account_number", "0690000034");
$payload->set("beneficiary_name", "Abraham Smith Olaolu");
$service = new Beneficiaries($config);
$request = $service->create($payload);
```

<br>

### Subscriptions

The following implementation shows how to activate a subscription, fetch a subscription, get all subscriptions.

```php
use Flutterwave\Service\Subscription;

# List Subscription
$service = new Subscription();
$response = $service->list();

# Activate Subscription
$service = new Subscription();
$response = $service->activate("4147");
```

### Bills

The following implementation shows how to pay for any kind of bill from Airtime to DSTv payments to Tolls. Please view the rave documentation section on Bill payment for different types of bill services you can pass into the ```payBill``` method as an```$array```.

visit: https://developer.flutterwave.com/v3.0/reference#buy-airtime-bill

```php
use Flutterwave\Payload;
use Flutterwave\Service\Bill;

$payload = new Payload();
$payload->set("country", "NG");
$payload->set("customer", "+2349067985861");
$payload->set("amount", "2000");
$payload->set("type", "AIRTIME");
$payload->set("reference", "TEST_".uniqid().uniqid());

$service = new Bill($config);
$request = $service->createPayment($payload);
```

### Virtual Accounts

The following implementation shows how to create a virtual Account. Please view the documentation for more options that can be added in the payload
https://developer.flutterwave.com/reference#create-a-virtual-account-number

```php
use Flutterwave\Service\VirtualAccount;

$service = new VirtualAccount();

$payload = [
    "email" => "kennyio@gmail.com",
    "bvn" => "12345678901",
];

$response = $service->create($payload);
```
<br>

### Tokenized Charge

Once the charge and validation process is complete for the first charge on the card, you can make use of the token for subsequent charges.

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => null,
    "additionalData" => [
        "token" => "flw-t0-fe20067f9d8d3ce3d06f93ea2d2fea28-m03k"
    ]
];

$data['redirectUrl'] = "http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php?tx_ref={$data['tx_ref']}";

$customerObj = $tokenpayment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "olaobajua@gmail.com",
    "phone" => "+2349067985861"
]);
$data['customer'] = $customerObj;
$tokenpayment = \Flutterwave\Flutterwave::create("tokenize");
$payload  = $tokenpayment->payload->create($data);
$result = $tokenpayment->initiate($payload);
```

### View Transactions

List all transactions on your account. You could do a specific query using ```customer_email``` or ```customer_fullname``` to make specifc search. View all successfull or failed transactions for a particular period, month or year

```php
# Transaction service
```

## Testing

All of the SDK's tests are written with PHP's ```phpunit``` module. The tests currently test:
```Account```,
```Card```,
```Transfer```,
```Preauth```,
```Collection Subaccount```,
```Payout Subaccount```,
```Subscriptions``` and
```Paymentplan```

They can be run like so:

```sh
phpunit
```

>**NOTE:** If the test fails for creating a subaccount, just change the ```account_number``` ```account_bank```  and ```businesss_email``` to something different

>**NOTE:** The test may fail for account validation - ``` Pending OTP validation``` depending on whether the service is down or not
<br>


<a id="debugging errors"></a>

## Debugging Errors
We understand that you may run into some errors while integrating our library. You can read more about our error messages [here](https://developer.flutterwave.com/docs/integration-guides/errors).

For `authorization` and `validation` error responses, double-check your API keys and request. If you get a `server` error, kindly engage the team for support.


<a id="support"></a>

## Support
For additional assistance using this library, contact the developer experience (DX) team via [email](mailto:developers@flutterwavego.com) or on [slack](https://bit.ly/34Vkzcg).

You can also follow us [@FlutterwaveEng](https://twitter.com/FlutterwaveEng) and let us know what you think 😊.


<a id="contribution-guidelines"></a>

## Contribution guidelines
Read more about our community contribution guidelines [here](/CONTRIBUTING.md)


<a id="license"></a>

## License

By contributing to this library, you agree that your contributions will be licensed under its [MIT license](/LICENSE).

Copyright (c) Flutterwave Inc.

<a id="references"></a>

## Flutterwave API  References

- [Flutterwave API Documentation](https://developer.flutterwave.com)
- [Flutterwave Dashboard](https://app.flutterwave.com)  
