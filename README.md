<p align="center">
    <img title="Flutterwave" height="200" src="https://flutterwave.com/images/logo/full.svg" width="50%"/>
</p>

# Flutterwave v3 PHP SDK.

![Packagist Downloads](https://img.shields.io/packagist/dt/flutterwavedev/flutterwave-v3)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/flutterwavedev/flutterwave-v3)
![GitHub stars](https://img.shields.io/github/stars/Flutterwave/Flutterwave-PHP-v3)
![Packagist License](https://img.shields.io/packagist/l/flutterwavedev/flutterwave-v3)

This Flutterwave v3 PHP Library provides easy access to Flutterwave for Business (F4B) v3 APIs from PHP application. It simplifies the complexities involved in direct integration and enables you to make quick API calls.

Available features include:

- Collections: Supported Payment Methods are Card, Account, Mobile money, Bank Transfers, USSD, Barter, and NQR.
- Payouts and Beneficiaries.
- Recurring Payments: Tokenization and Subscriptions.
- Split Payments: Easily split payments among mutliple subaccounts(vendors).
- Card Issuing.
- Transactions Dispute Management: Handle refunds and manage disputes.
- Transaction Reporting: Collections, Payouts, Settlements, and Refunds.
- Bill Payments: Airtime, Data bundle, Cable, Power, Toll, E-bills, and Remitta.
- Identity Verification:  Account Verification, and BVN Verification.

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

1. Your Flutterwave [API Keys](https://developer.flutterwave.com/v3.0.0/docs/authentication).
2. Acceptable PHP versions are 7.4.0 or higher. For older versions of PHP, please use the [Legacy Branch]( https://github.com/Flutterwave/PHP-v3/tree/legacy).


<a id="installation"></a>

## Installation

### Download Release Artifact
If you prefer not to use a composer. each [release](https://github.com/Flutterwave/PHP-v3/releases/) contains a zip file with all the necessary dependencies installed. Simply download the version that corresponds to your PHP version.

### Installation via Composer.

To install the package via Composer, run the following command:
```shell
composer require flutterwavedev/flutterwave-v3
```

<a id="initialization"></a>

## Initialization

Create a .env file by following the format provided in the `.env.example` file.
Save your PUBLIC_KEY, SECRET_KEY, and ENV values in the `.env` file.

```bash
cp .env.example .env
```
Your `.env` file should look this.

```env
FLW_PUBLIC_KEY=FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X
FLW_SECRET_KEY=FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X
FLW_ENCRYPTION_KEY=FLWSECK_XXXXXXXXXXXXXXXX
FLW_ENV='staging/production'
FLW_LOG_DIR=logs
```

### Render Payment Modal

The SDK provides two easy methods of accepting collections via the popular payment modal. [Learn more](#)

1. [Flutterwave Inline](https://developer.flutterwave.com/v3.0.0/docs/inline).
2. [Flutterwave Standard](https://developer.flutterwave.com/v3.0.0/docs/flutterwave-standard-1).


### Get Started

Edit the `paymentForm.php` and `processPayment.php` files to meet your needs. Both files are well documented for your convenience.

To process a payment, simply redirect to the `paymentForm.php` file in your browser .

In this implementation, we are expecting a form encoded POST request to this script.
The request will contain the following parameters:

```json

 {
    "amount": "The amount to be charged. (*)",
    "currency": "The currency to charge in. (*)",
    "first_name": "The first name of the customer. (*)",
    "last_name" : "The last name of the customer. (*)",
    "email": "The customers email address. (*)",
    "phone_number": "The customer's phone number. (Optional).",
    "success_url": "The url to redirect customer to after successful payment.",
    "failure_url": "The url to redirect customer to after a failed payment.",
    "tx_ref":"The unique transaction identifier. if ommited the apiclient would generate one."
 }

```

The script in `paymentProcess.php` handles the request data via the `PaymentController`. If you are using a Framework like Laravel or CodeIgniter you might want to take a look at the [PaymentController](#).

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

# normal configuration
Flutterwave::bootstrap(); # this will use the default configuration set in .env
```

if you prefer not to use a .env, you can simply pass your API keys directly like the example below.

```php
use \Flutterwave\Helper\Config;

$myConfig = Config::setUp(
    'FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
    'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X',
    'FLWSECK_XXXXXXXXXXXXXXXX',
    'staging'
);
 
Flutterwave::bootstrap($myConfig);
````

<a id="usage"></a>

## Usage

### Charge
1. [Account Charge](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#account)
2. [ACH Charge](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#ach)
3. [Card Charge](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#card)
4. [Mobile Money](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#mobile-money)
5. [FawryPay](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#fawry-pay)
6. [GooglePay](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#google-pay)
7. [ApplePay](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#apple-pay)
8. [Mpesa](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#mpesa)
9. [BankTransfer](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#bank-transfers)
10. [USSD](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#ussd)
11. [eNaira](https://github.com/Flutterwave/PHP-v3/wiki/Direct-Charge#enaira)

### Resources
1. [Banks](https://github.com/Flutterwave/PHP-v3/wiki/Banks)
2. [Beneficiaries](https://github.com/Flutterwave/PHP-v3/wiki/Beneficiaries)
3. [Payment Plans](https://github.com/Flutterwave/PHP-v3/wiki/Payment-Plan)
4. [Collection Subaccounts](https://github.com/Flutterwave/PHP-v3/wiki/Collection-Subaccounts)
5. [Payout Subaccounts](https://github.com/Flutterwave/PHP-v3/wiki/Payout-Subaccounts)
6. [Subscriptions](https://github.com/Flutterwave/PHP-v3/wiki/Subscriptions)
7. [Transfers](https://github.com/Flutterwave/PHP-v3/wiki/Transfer-(Payouts))
8. [Transactions](https://github.com/Flutterwave/PHP-v3/wiki/Transactions)
9. [Virtual Cards](https://github.com/Flutterwave/PHP-v3/wiki/Virtual-Cards)
10. [Virtual Account](https://github.com/Flutterwave/PHP-v3/wiki/Virtual-Account)
11. [Misc](https://github.com/Flutterwave/PHP-v3/wiki/Misc)

## Testing

All of the SDK's tests are written with PHP's ```phpunit``` module. The tests currently cover the following features:

```Account```,

```Card```,

```Transfer```,

```Preauth```,

```Collection Subaccount```,

```Payout Subaccount```,

```Subscriptions``` ,

```Paymentplan```.

You can run the tests by exexuting the following command:
```sh
phpunit
```

>**NOTE:** If the test fails for subaccount creation, just change the values of the ```account_number``` ,```account_bank```  and ```businesss_email```.

>**NOTE:** The test may fail for account validation - ``` Pending OTP validation``` depending on whether the service is down or not.
<br>


<a id="debugging errors"></a>

## Debugging Errors
We understand that you may encounter errors while integrating our library. You can read more about our error messages [here](https://developer.flutterwave.com/v3.0.0/docs/common-errors).

For `authorization` and `validation` error responses, please double-check your API keys and request. If you get a `server` error, kindly reach out to our support team for assistance.


<a id="support"></a>

## Support
For additional assistance using this library, contact the developer experience (DX) team via [email](mailto:developers@flutterwavego.com) or on [slack](https://bit.ly/34Vkzcg).

You can also follow us [@FlutterwaveEng](https://twitter.com/FlutterwaveEng) and let us know what you think 😊.


<a id="contribution-guidelines"></a>

## Contribution guidelines
Read more about our community contribution guidelines [here](/CONTRIBUTING.md).


<a id="license"></a>

## License

By contributing to this library, you agree that your contributions will be licensed under its [MIT license](/LICENSE).

Copyright (c) Flutterwave Inc.

<a id="references"></a>

## Flutterwave API  References

- [Flutterwave API Documentation](https://developer.flutterwave.com/v3.0.0/docs).
- [Flutterwave Dashboard](https://app.flutterwave.com).  
