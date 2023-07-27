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
2. Acceptable PHP versions: >= 7.4.0. for older versions of PHP use the [Legacy Branch]( https://github.com/Flutterwave/PHP-v3/tree/legacy )


<a id="installation"></a>

## Installation

### Installation via Composer.

To install the package via Composer, run the following command.
```shell
composer require flutterwavedev/flutterwave-v3
```

<a id="initialization"></a>

## Initialization

Create a .env file and follow the format of the `.env.example` file
Save your PUBLIC_KEY, SECRET_KEY, ENV in the `.env` file

```bash
cp .env.example .env
```
Your `.env` file should look this.

```env
PUBLIC_KEY=FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X
SECRET_KEY=FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X
ENCRYPTION_KEY=FLWSECK_XXXXXXXXXXXXXXXX
ENV='staging/production'
```


<a id="usage"></a>

## Usage

### Render Payment Modal

The SDK provides two easy methods of making collections via the famous payment modal. [Learn more](#)

1. [Flutterwave Inline]( https://developer.flutterwave.com/docs/collecting-payments/inline )
2. [Flutterwave Standard]( https://developer.flutterwave.com/docs/collecting-payments/standard )

### Get Started



Edit the `paymentForm.php` and `processPayment.php` files to suit your purpose. Both files are well documented.

Simply redirect to the `paymentForm.php` file on your browser to process a payment.

In this implementation, we are expecting a form encoded POST request to this script.
The request will contain the following parameters.

```json

 {
    "amount": "The amount required to be charged. (*)",
    "currency": "The currency to charge in. (*)",
    "first_name": "The first name of the customer. (*)",
    "last_name" : "The last name of the customer. (*)",
    "email": "The customers email address. (*)",
    "phone_number": "The customer's phone number. (Optional).",
    "success_url": "The url to redirect customer to after successful payment.",
    "failure_url": "The url to redirect customer to after a failed payment.",
    "tx_ref":"The unique transaction identifier. if ommited the apiclient would generate one"
 }

```

The script in `paymentProcess.php` handles the request data via the `PaymentController`. If you are using a Framework like Laravel or CodeIgniter you might want to take a look at the [PaymentController](#)

```php
<?php

declare(strict_types=1);

# if vendor file is not present, notify developer to run composer install.
require __DIR__.'/vendor/autoload.php';

use Flutterwave\Controller\PaymentController;
use Flutterwave\EventHandlers\ModalEventHandler as PaymentHandler;
use Flutterwave\Flutterwave;
use Flutterwave\Library\Modal;

# start a session.
session_start();

try {
    Flutterwave::bootstrap();
    $customHandler = new PaymentHandler();
    $client = new Flutterwave();
    $modalType = Modal::POPUP; // Modal::POPUP or Modal::STANDARD
    $controller = new PaymentController( $client, $customHandler, $modalType );
} catch(\Exception $e ) {
    echo $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = $_REQUEST;
    $request['redirect_url'] = $_SERVER['HTTP_ORIGIN'] . $_SERVER['REQUEST_URI'];
    try {
        $controller->process( $request );
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
}

$request = $_GET;
# Confirming Payment.
if(isset($request['tx_ref'])) {
    $controller->callback( $request );
} else {
    
}
exit();

```
<br>

### Configuration settings
Create a .env file and add the bootstrap method first before initiating a charge.
```php
use \Flutterwave\Flutterwave;
use \Flutterwave\Helper\Config;
# normal configuration
Flutterwave::bootstrap(); # this will use the default configuration set in .env

# for a custom configuration
# your config must implement Flutterwave\Contract\ConfigInterface
$myConfig = Config::setUp(
    'FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
    'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
    'FLWSECK_XXXXXXXXXXXXXXXX',
    'staging'
); 
Flutterwave::bootstrap($myConfig);
```

### Account Charge

The following implementation shows how to initiate a direct bank charge. <br /> 
want to see it work real time? a quick sample implementation  can be found [here](https://github.com/Flutterwave/PHP/blob/fix/add-support-for-php7-8/examples/account.php).

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::NGN, // or EUR or GBP for EU Collection.
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
    "email" => "ola2fhahfj@gmail.com",
    "phone" => "+234900154861"
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

### FawryPay

Receive Fawry payments from customers in Egypt. Read the [overview](https://developer.flutterwave.com/docs/direct-charge/fawry) for this payment method before proceeding.

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "currency" => Currency::EGP,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => "https://example.com"
];

$payment = \Flutterwave\Flutterwave::create("fawry");
$customerObj = $payment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "vicomma@gmail.com",
    "phone" => "+2349060085861"
]);

$data['customer'] = $customerObj;
$payload  = $payment->payload->create($data);
$result = $payment->initiate($payload);
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
use Flutterwave\Util\Currency;

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
    "email" => "38djsdjfjc954@gmail.com",
    "phone" => "+234900085861"
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

### Enaira

```php
use Flutterwave\Util\Currency;

$data = [
    "amount" => 2000,
    "is_token" => 1,
    "currency" => Currency::NGN,
    "tx_ref" => uniqid().time(),
    "redirectUrl" => "https://example.com"
];

$payment = \Flutterwave\Flutterwave::create("enaira");
$customerObj = $payment->customer->create([
    "full_name" => "Olaobaju Jesulayomi Abraham",
    "email" => "developers@flutterwavego.com",
    "phone" => "+2349060085861"
]);

$data['customer'] = $customerObj;
$payload  = $payment->payload->create($data);
$result = $payment->initiate($payload);
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
    "email" => "ola3785yfhf@gmail.com",
    "phone" => "+2349062947561"
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
