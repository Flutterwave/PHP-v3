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
$ composer install flutterwavedev/flutterwave-v3
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
// Prevent direct access to this class
define("BASEPATH", 1);

include('library/rave.php');
include('library/raveEventHandlerInterface.php');


use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Rave;


$URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$getData = $_GET;
$postData = $_POST;
$publicKey = $_SERVER['PUBLIC_KEY'];
$secretKey = $_SERVER['SECRET_KEY'];
$success_url = $postData['successurl'];
$failure_url = $postData['failureurl'];
$env = $_SERVER['ENV'];

if($postData['amount']){
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
if($postData['ref']){
    $prefix = $postData['ref'];
    $overrideRef = true;
}

$payment = new Rave($_SESSION['secretKey'], $prefix, $overrideRef);

function getURL($url,$data = array()){
    $urlArr = explode('?',$url);
    $params = array_merge($_GET, $data);
    $new_query_string = http_build_query($params).'&'.$urlArr[1];
    $newUrl = $urlArr[0].'?'.$new_query_string;
    return $newUrl;
};
```

In order to handle events that at occurs at different transaction stages. You define a class that implements the ```EventHandlerInterface```

```php
class myEventHandler implements EventHandlerInterface{
    /**
     * This is called when the Rave class is initialized
     **/
    function onInit($initializationData){
        // Save the transaction to your DB.
    }
    
    /**
     * This is called only when a transaction is successful
     **/

    function onSuccessful($transactionData) {
        /** 
        * Get the transaction from your DB using the transaction reference (txref). 
        * Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue. 
        * Comfirm that the transaction is successful
        * Confirm that the currency on your db transaction is equal to the returned currency
        * Confirm that the db transaction amount is equal to the returned amount
        * Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        * Give value for the transaction
        * Update the transaction to note that you have given value for the transaction
        * You can also redirect to your success page from here
        **/

        if ($transactionData->status == 'success'){
          if ($transactionData->currency == $_SESSION['currency'] && $transactionData->amount == $_SESSION['amount']){
              
              if ($_SESSION['publicKey']){
                    header('Location: '.getURL($_SESSION['successurl'], array('event' => 'successful')));
                    $_SESSION = array();
                    session_destroy();
                }
          } else {
              if($_SESSION['publicKey']){
                    header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'suspicious')));
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
     **/
    function onFailure($transactionData){
        /**
        * Get the transaction from your DB using the transaction reference (txref)
        * Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        * You can also redirect to your failure page from here
        **/

        if ($_SESSION['publicKey']) {
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'failed')));
            $_SESSION = array();
            session_destroy();
        }
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     **/
    function onRequery($transactionReference){
        // Do something, anything!
    }
    
    /**
     * This is called a transaction requery returns with an error
     **/
    function onRequeryError($requeryResponse){
        // Do something, anything!
    }
    
    /**
     * This is called when a transaction is canceled by the user
     **/
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
        if($_SESSION['publicKey']){
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'canceled')));
            $_SESSION = array();
            session_destroy();
        }
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     **/
    function onTimeout($transactionReference, $data){
        /**
        * Get the transaction from your DB using the transaction reference (txref)
        * Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        * Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
        **/
        if($_SESSION['publicKey']){
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'timedout')));
            $_SESSION = array();
            session_destroy();
        }
    }
}

if($postData['amount']){
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
}else{
    if($getData['cancelled'] && $getData['tx_ref']){
        // Handle canceled payments
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['tx_ref'])
        ->paymentCanceled($getData['tx_ref']);
    }elseif($getData['tx_ref']){
        // Handle completed payments
        $payment->logger->notice('Payment completed. Now requerying payment.');
        
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['tx_ref']);
    }else{
        $payment->logger->warn('Stop!!! Please pass the txref parameter!');
        echo 'Stop!!! Please pass the txref parameter!';
    }
}
```
<br>

### Account Charge

The following implementation shows how to initiate a direct bank charge. Use the Playground DIrectory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/AccountPayment.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\AccountPayment;

//The data variable holds the payload
$data = array(
    "amount" => "3000",
    "type" => "debit_ng_account",//debit_ng_account or debit_uk_account
    "account_bank" => "044",
    "account_number" => "0690000037",
    "currency" => "NGN",//NGN or GBP
    "email" => "olaobajua@gmail.com",
    "phone_number" => "07067965809",
    "fullname" => "Olaobaju Abraham",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );

$payment = new AccountPayment();
$result = $payment->accountCharge($data);

if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}
print_r($result);
```
<br>

### ACH Charge

The following implementation shows how to accept payments directly from customers in the US and South Africa. Use the Playground DIrectory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/AchPayment.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\AchPayment;

// The data variable holds the payload
$data = array(
    "tx_ref" =>  "MC-1585230ew9v5050e8",
    "amount" => "100",
    "type" => "ach_payment",
    "currency" => "USD",
    "country" => "US",
    "email" => "ekene@gmail.com",
    "phone_number" => "0902620185",
    "fullname" => "Ekene Eze",
    "redirect_url" => "http://ekene.com/u/payment-completed",
    );

$payment = new AchPayment();

$result = $payment->achCharge($data);
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}
print_r($result);

```

<br>

### Direct Card Charge

The following implementation shows how to initiate a card charge. Use the Playground Directory to view an implementation Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/CardPayment.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\CardPayment;

//The data variable holds the payload
$data = array(
    "card_number"=> "5531886652142950",
    "cvv"=> "564",
    "expiry_month"=> "09",
    "expiry_year"=> "22",
    "currency"=> "NGN",
    "amount" => "1000",
    "fullname"=> "Ekene Eze",
    "email"=> "ekene@flw.com",
    "phone_number"=> "0902620185",
    "fullname" => "temi desola",
    //"tx_ref"=> "MC-3243e",// should be unique for every transaction
    "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
           
    );

$payment = new CardPayment();
$res = $payment->cardCharge($data);//This call is to figure out the authmodel
$data['authorization']['mode'] = $res['meta']['authorization']['mode'];

if($res['meta']['authorization']['mode'] == 'pin'){

    //Supply authorization pin here
    $data['authorization']['pin'] = '3310';
}

if($res['meta']['authorization']['mode'] == 'avs_noauth'){
    //supply avs details here
    $data["authorization"] = array(
            "mode" => "avs_noauth",
            "city"=> "Sampleville",
            "address"=> "3310 sample street ",
            "state"=> "Simplicity",
            "country"=> "Simple",
            "zipcode"=> "000000",
        );
}

$result = $payment->cardCharge($data);//charge with new fields
// print_r($result);//this returns the an array

if($result['data']['auth_mode'] == 'otp'){
    $id = $result['data']['id'];
    $flw_ref = $result['data']['flw_ref'];
    $otp = '12345';
    $validate = $payment->validateTransaction($otp,$flw_ref);// you can print_r($validate) to see the response
    $verify = $payment->verifyTransaction($id);
}
```

### Mobile Money Payments

The following implementation shows how to initiate a mobile money payment. Use the Playground Directory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/MobileMoney.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\MobileMoney;

// The data variable holds the payload
$data = array(
    "order_id" => "USS_URG_89245453s2323",
    "amount" => "1500",
    "type" => "mobile_money_rwanda",// could be mobile_money_rwanda,mobile_money_uganda, mobile_money_zambia, mobile_money_ghana
    "currency" => "RWF",
    "email" => "ekene@flw.com",
    "phone_number" => "054709929220",
    "fullname" => "John Madakin",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );


$payment = new MobileMoney();
$result = $payment->mobilemoney($data);
$id = $result['data']['id'];
$verify = $payment->verifyTransaction($id);
$print_r($result);
```

### USSD

Collect payments via ussd

```php
require("Flutterwave-Rave-PHP-SDK/library/Ussd.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Ussd;
//The data variable holds the payload
$data = array(
        "tx_ref" => "MC-15852309v5050e8",
        "account_bank" => "058",
        "amount" => "1500",
        "currency" =>"NGN",
        "email" =>"user@gmail.com",
        "phone_number" =>"054709929220",
        "fullname" => "John Madakin",
        
      
    );

$payment = new Ussd();
$result = $payment->ussd($data);//initiates the charge

echo 'Dial '.$result['meta']['authorization']['note'].' to authorize your transaction';

//A webhook notification would be sent to you....

if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}

```

<br>

### Mpesa

Collect payments from your customers via Mpesa.

```php
require("Flutterwave-Rave-PHP-SDK/library/Mpesa.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Mpesa;

$data = array(
    "amount" => "1500",
    "type" => "mpesa",
    "currency" => "KES",
    "email" => "ekene@flw.com",
    "phone_number" => "054709929220",
    "fullname" => "Ekene Eze",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );

$payment = new Mpesa();

$result = $payment->mpesa($data);
print_r($result);
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}
```

### Transfer Implementation

How to make a transfer payment

```php

require("Flutterwave-Rave-PHP-SDK/library/Transfer.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Transfer;

// sample payload for payBill()
$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000040",
    "amount"=> 5500,
    "narration"=> "Akhlm Pstmn Trnsfr xx007",
    "currency"=> "NGN",
    "reference"=> "akhlm-pstmnpyt-rfxx007_PMCKDU_1",// read the docs about testing successful and failed transaction.
    "callback_url"=> "https://webhook.site/b3e505b0-fe02-430e-a538-22bbbce8ce0d",
    "debit_currency"=> "NGN"
);

//sample payload for bulkBill()
$bulkdata = array(
  "title"=> "Staff salary",
  "bulk_data"=> array(
      array(
          "bank_code"=> "044",
          "account_number"=> "0690000032",
          "amount"=> 45000,
          "currency"=> "NGN",
          "narration"=> "akhlm blktrnsfr",
          "reference"=> "akhlm-blktrnsfr-xx03"
      ),
      array(
          "bank_code"=> "044",
          "account_number"=> "0690000034",
          "amount"=> 5000,
          "currency"=> "NGN",
          "narration"=> "akhlm blktrnsfr",
          "reference"=> "akhlm-blktrnsfr-xy03"
      ))
);

$getdata = array(
    //"reference"=>"edf-12de5223d2f32434753432"
     "id"=>"BIL136",
     "product_id"=>"OT150"
);

$listdata = array(
  'status'=>'failed'
);

$feedata = array(
'currency'=> 'NGN', //if currency is omitted. the default currency of NGN would be used.
'amount'=> 1000
);

$payment = new Transfer();
$result = $payment->singleTransfer($data);//initiate single transfer payment
$createBulkTransfer = $payment->bulkTransfer($bulkdata);// get bulk result....
$transfers = $payment->listTransfers($listdata);//you can add a payload for the page. you can remove the array if want to get it all.
$getTransferFee = $payment->getTransferFee($feedata);
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}

```

<br>

### Vitual Cards

The following implementation shows how to create virtual cards on rave. Use the Playground Directory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/VirtualCards.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VirtualCard;

$data = array(
    "currency"=>"NGN",
    "amount"=>20000,
    "billing_name"=>"Jermaine Graham",
    "billing_address"=>"2014 Forest Hills Drive",
    "billing_city"=>"Node",
    "billing_state"=>"Javascript",
    "billing_postal_code"=>"000009",
    "billing_country"=>"NG",
    "callback_url"=>"https://webhook.site/96374895-154d-4aa0-99b5-709a0a128674"
    );

    $trns_data = array('id'=> 'a41de883-c8da-45a0-9b23-37780c88285f');
    $getCardData = array('id'=>'7a81d279-a07a-4775-a55a-5fa2c98e20ae');
    $terminate_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb');
    $fund_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'amount'=>'2000', 'debit_currency'=>'NGN');
    $withdraw_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'amount'=>'500');
    $blockCard_data = array('id' => '1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'status_action'=>'block');

    $card = new VirtualCard();
    $createCard = $card->createCard($data);//initiates the charge
    $getCard = $card->getCard($getCardData);
    $getCards = $card->listCards();
    $terminate = $card->terminateCard($terminate_data);
    $fund = $card->fundCard($fund_data);
    $transactions = $card->cardTransactions($trns_data);
    $withdraw = $card->cardWithdrawal($withdraw_data);
    $block_unblock = $card->block_unblock_card($blockCard_data);
    print_r($createCard);
```

### BVN Verification

The following implementation shows how to verify a Bank Verification Number.

```php
require("Flutterwave-Rave-PHP-SDK/library/Bvn.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Bvn;
//The data variable holds the payload
$bvn_number = "123456789";
$bvn = new Bvn();
$result = $bvn->verifyBVN($bvn_number);
print_r($result);
```

<br>

### Payment Plans

The following implementation shows how to create a payment plan on the rave dashboard. Use the Playground Directory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/PaymentPlan.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\PaymentPlan;

//sample payload for payBill()
$data = array(
    "amount"=> 2000,
    "name"=> "plan 2",
    "interval"=> "monthly",
    "duration"=> 48
);
$update = array( "id" => "5356","name" => "The Game","status" => "Active");
$getdata = array("id"=>"5116");

$payment = new PaymentPlan();
$result = $payment->createPlan($data);//create a Plan reciept
$updateResult = $payment->updatePlan($update);//update a plan....
$paymentPlans = $payment->getPlans();//list all payment plans....
$aPlan = $payment->get_a_plan($getdata);//get a payment plans....
print_r($result);
```

<br>

### Subaccount Management

The following implementation shows how to create a subaccount on the rave dashboard
Use the Playground Directory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/Subaccount.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Subaccount;

$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000037",
    "business_name"=> "Eternal Blue",
    "business_email"=> "petya@stux.net",
    "business_contact"=> "Anonymous",
    "business_contact_mobile"=> "090890382",
    "business_mobile"=> "09087930450",
    "country"=> "NG",
    "meta"=> array(
        array(
            "meta_name"=> "mem_adr",
            "meta_value"=> "0x16241F327213"
        )
    ),
    "split_type"=> "percentage",
    "split_value"=> 0.5
);

$fetch_data = array("id" => "RS_9247C52A37C5EB15C7E8E974CD1B35D7");
$update_data = array("id" => "2755","business_name"=>"Mad O!","business_email"=> "mad@o.enterprises",
"account_bank"=> "044","account_number"=> "0690000040","split_type"=> "flat","split_value"=> "200");

$subaccount = new Subaccount();
$createSubaccount = $subaccount->createSubaccount($data);
$getSubaccounts = $subaccount->getSubaccounts();
$fetchSubaccount = $subaccount->fetchSubaccount($fetch_data);
$updateSubaccount = $subaccount->updateSubaccount($update_data);
print_r($createSubaccount);
```

<br>

### Transfer Recipient

The following implementation shows how to create a transfer recipient on the rave dashboard. Use the Playground Directory to view Responses and samples of use.

```php
require("Flutterwave-Rave-PHP-SDK/library/Recipient.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Recipient;

$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000036",
);
$fetchdata = array(
  'id' => '6153'
);
$deldata = array(
  'id'=>'7236'
);

$payment = new Recipient();
$recipient1 = $payment->createRecipient($data);//Create a recipient for transfer
$recipients = $payment->listRecipients();// get all existing recipients
$recipient = $payment->fetchBeneficiary($fetchdata);//fetch a specific recipient.
$deleteRecipient = $payment->deleteBeneficiary($deldata);//delete recipient
print_r($recipient1);
```

<br>

### Subscriptions

The following implementation shows how to activate a subscription, fetch a subscription, get all subscriptions.

```php
require("Flutterwave-Rave-PHP-SDK/library/Subscription.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Subscription;

$id = 1112 //Id of subscription plan
//$cid = 2222
$subscription = new Subscription();
$resultGet = $subscription->getAllSubscription();//gets all existing subscription
$resultActivate = $subscription->activateSubscription($id);// activates a subscription plan
$resultCancel = $subscription->cancelSubscription($cid);// activates a subscription plan

//returns the result 
print_r($result);
```

### Bills

The following implementation shows how to pay for any kind of bill from Airtime to DSTv payments to Tolls. Please view the rave documentation section on Bill payment for different types of bill services you can pass into the ```payBill``` method as an```$array```.

visit: https://developer.flutterwave.com/v3.0/reference#buy-airtime-bill

```php
require("Flutterwave-Rave-PHP-SDK/library/Bill.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Bill;

$data = array(
    "country"=> "NG",
	"customer"=> "+23490803840303",
	"amount"=> 500,
	"recurrence"=> "ONCE",
	"type"=> "AIRTIME",
	"reference"=> "9300049645534545454332433"
);

//sample payload for bulkBill()
$bulkdata = array(
    "bulk_reference"=>"edf-12de5223d2f3243474543",
    "callback_url"=>"https://webhook.site/96374895-154d-4aa0-99b5-709a0a128674",
    "bulk_data"=> array(
        array(
        "country"=> "NG",
        "customer"=> "+23490803840303",
        "amount"=> 500,
        "recurrence"=> "WEEKLY",
        "type"=> "AIRTIME",
        "reference"=>"930049200929"
        ),
        array(
        "country"=>"NG",
        "customer"=> "+23490803840304",
        "amount"=> 500,
        "recurrence"=> "WEEKLY",
        "type"=>"AIRTIME",
        "reference"=>"930004912332434232"
        )
    ),
);

$getdata = array(
    //"reference"=>"edf-12de5223d2f32434753432"
     "id"=>"BIL136",
     "product_id"=>"OT150"
);

$payment = new Bill();
$result = $payment->payBill($data);//create a bill paymenr
$bulkresult = $payment->bulkBill($bulkdata);//create bulk bill payment....
$getresult = $payment->getBill($getdata);// get bulk result....
$getAgencies = $payment->getAgencies();
$getBillCategories = $payment->getBillCategories();
print_r($result);
```

### Ebills

The following implementation shows how to create a electronic receipt.

```php
require("Flutterwave-Rave-PHP-SDK/library/Ebill.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Ebill;

$data = array(
    "narration"=> "mndkn blls",
    "number_of_units"=> 2,//should be a string
    "currency"=> "NGN",
    "amount"=> 200,//should be a string
    "phone_number"=> "09384747474",
    "email"=>"jake@rad.com",
    "tx_ref"=> "akhlm-pstmn-1094434370393",
    "ip"=> "127.9.0.7",
    "custom_business_name"=> "John Madakin",
    "country"=> "NG"
);

$update = array(
    "reference"=>"RVEBLS-2B93A7039017-90937",//on creation of order, this is the flw_ref
    "currency"=> "NGN",
    "amount"=> "4000"
);

$payment = new Ebill();
$result = $payment->order($data);//create an order reciept
$updateResult = $payment->updateOrder($update);//create bulk bill payment....
print_r($result);
```

### Virtual Accounts

The following implementation shows how to create a virtual Account. Please view the documentation for more options that can be added in the payload
https://developer.flutterwave.com/reference#create-a-virtual-account-number

```php
require("Flutterwave-Rave-PHP-SDK/library/VirtualAccount.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VirtualAccount;

//sample payload for payBill()
$data = array(
  "email"=> "johnmadakin@allstar.com",
  "duration"=> 5,
  "frequency"=> 5,
  "amount"=>"22000",
  "is_permanent"=> true,
  "tx_ref"=> "jhn-mdkn-101923123463"
);

$bulkdata = array(
  "accounts"=> 5,
  "email"=> "sam@son.com",
  "is_permanent"=> true,
  "tx_ref"=> "jhn-mndkn-012439283422"
);

$batch = array('batch_id' => 'RND_2641579516055928');

$getdata = array(
    "order_ref"=>"URF_1590362018488_8875935"
);

$account = new VirtualAccount();
$result = $account->createVirtualAccount($data);//create a virtak account
$bulkAccounts = $account->createBulkAccounts($bulkdata);//create bulk v accounts
$virtualAccounts = $account->getBulkAccounts($batch);//list all bulk accounts
$virtualAccount = $account->getAccountNumber($getdata);//get an account.
print_r($result);
```
<br>

### Tokenized Charge

Once the charge and validation process is complete for the first charge on the card, you can make use of the token for subsequent charges.

```php
require("Flutterwave-Rave-PHP-SDK/library/TokenizedCharge.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\TokenizedCharge;

$data = array(
     "token"=> "flw-t1nf-1ff187b04cecb4acff4ac62c2b6f7784-m03k",
     "currency"=> "NGN",
     "country"=> "NG",
     "amount"=> 30300,
     "email"=> "olaobajua@gmail.com",
     "first_name"=> "Anonymous",
     "last_name"=> "customer",
     "client_ip" =>"154.123.220.1",
     "device_fingerprint" =>"62wd23423rq324323qew1" 
    );

$payment = new TokinizedCharge();
$result = $payment->tokenCharge($data);//initiates the charge
$verify = $payment->verifyTransaction();
print_r($result);
```
<br>

### View Transactions

List all transactions on your account. You could do a specific query using ```customer_email``` or ```customer_fullname``` to make specifc search. View all successfull or failed transactions for a particular period, month or year

```php
require("Flutterwave-Rave-PHP-SDK/library/Transactions.php");
use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Transactions;

$data = array(
'amount'=> 1000
);
$fetch_data = array(
'id'=>'345522'
);
$time_data = array(
  'id'=>'3434'
);

$history = new Transactions();
$transactions = $history->viewTransactions();
$transactionfee = $history->getTransactionFee($data);
$verifyTransaction = $history->verifyTransaction($fetch_data);
$timeline = $history->viewTimeline($time_data);
print_r($transactions);
```
<br>

### Voucher payment

Collect ZAR payments offline using Vouchers

```php
require("Flutterwave-Rave-PHP-SDK/library/VoucherPayment.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VoucherPayment;
//The data variable holds the payload
$data = array(
        //"public_key": "FLWPUBK-6c4e3dcb21282d44f907c9c9ca7609cb-X"//you can ommit the public key as the key is take from your .env file
        //"tx_ref": "MC-15852309v5050e8",
        "amount"=> "100",
        "type"=> "voucher_payment",
        "currency"=> "ZAR",
        "pin"=> "19203804939000",
        "email"=>"ekene@flw.com",
        "phone_number" =>"0902620185",
        "account_bank" => "058",
        "fullname" => "Ekene Eze",
        "client_ip" =>"154.123.220.1",
        "device_fingerprint" =>"62wd23423rq324323qew1",
        "meta" => array(
            "flightID"=> "123949494DC"
        )     
    );

$payment = new VoucherPayment();
$result = $payment->voucher($data);
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}
print_r($result);
```

You can also find the class documentation in the docs folder. There you will find documentation for the `Rave` class and the `EventHandlerInterface`.


## Testing

All of the SDK's tests are written with PHP's ```phpunit``` module. The tests currently test:
```Account```,
```Card```,
```Transfer```,
```Preauth```,
```Subaccount```,
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
