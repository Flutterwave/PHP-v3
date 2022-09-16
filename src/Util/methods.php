<?php

use Flutterwave\Service\AccountPayment;
use Flutterwave\Service\AchPayment;
use Flutterwave\Service\ApplePay;
use Flutterwave\Service\Banks;
use Flutterwave\Service\BankTransfer;
use Flutterwave\Service\Bill;
use Flutterwave\Service\CardPayment;
use Flutterwave\Service\ChargeBacks;
use Flutterwave\Service\CollectionSubaccount;
use Flutterwave\Service\Misc;
use Flutterwave\Service\MobileMoney;
use Flutterwave\Service\Mpesa;
use Flutterwave\Service\Otps;
use Flutterwave\Service\PaymentPlan;
use Flutterwave\Service\PayoutSubaccount;
use Flutterwave\Service\Preauth;
use Flutterwave\Service\Settlement;
use Flutterwave\Service\Subscription;
use Flutterwave\Service\TokenizedCharge;
use Flutterwave\Service\Transactions;
use Flutterwave\Service\Transfer;
use Flutterwave\Service\Ussd;
use Flutterwave\Service\VirtualAccount;
use Flutterwave\Service\VirtualCard;
//use Flutterwave\Service\PayPal;
//use Flutterwave\Service\Remita;
//use Flutterwave\Service\VoucherPayment;

return [
    "account" => AccountPayment::class,
    "ach" => AchPayment::class,
    "apple" => ApplePay::class,
    "bank" => Banks::class,
    "bank-transfer" => BankTransfer::class,
    "bill" => Bill::class,
    "card" => CardPayment::class,
    "chargeback" => ChargeBacks::class,
    "collection-subaccount" => CollectionSubaccount::class,
    "Misc" => Misc::class,
    "momo" => MobileMoney::class,
    "mpesa" => Mpesa::class,
    "otp" => Otps::class,
    "plan" => PaymentPlan::class,
    "payout-subaccount" => PayoutSubaccount::class,
    "preauth" => Preauth::class,
    "settlement" => Settlement::class,
    "subscription" => Subscription::class,
    "tokenize" => TokenizedCharge::class,
    "transaction" => Transactions::class,
    "transfer" => Transfer::class,
    "ussd" => Ussd::class,
    "virtual-account" => VirtualAccount::class,
    "virtual-card" => VirtualCard::class,
//    "paypal" => PayPal::class,
//    "remita" => Remita::class,
//    "voucher" => VoucherPayment::class,
];