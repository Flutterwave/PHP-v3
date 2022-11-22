<?php

declare(strict_types=1);

use Flutterwave\Service\AccountPayment;
use Flutterwave\Service\AchPayment;
use Flutterwave\Service\ApplePay;
use Flutterwave\Service\BankTransfer;
use Flutterwave\Service\Bill;
use Flutterwave\Service\CardPayment;
use Flutterwave\Service\ChargeBacks;
use Flutterwave\Service\Misc;
use Flutterwave\Service\MobileMoney;
use Flutterwave\Service\Mpesa;
use Flutterwave\Service\Preauth;
use Flutterwave\Service\TokenizedCharge;
use Flutterwave\Service\Transfer;
use Flutterwave\Service\Ussd;
//use Flutterwave\Service\PayPal;
//use Flutterwave\Service\Remita;
//use Flutterwave\Service\VoucherPayment;

return [
    'account' => AccountPayment::class,
    'ach' => AchPayment::class,
    'apple' => ApplePay::class,
    'bank-transfer' => BankTransfer::class,
    'bill' => Bill::class,
    'card' => CardPayment::class,
    'chargeback' => ChargeBacks::class,
    'Misc' => Misc::class,
    'momo' => MobileMoney::class,
    'mpesa' => Mpesa::class,
    'preauth' => Preauth::class,
    'tokenize' => TokenizedCharge::class,
    'transfer' => Transfer::class,
    'ussd' => Ussd::class,
//    "paypal" => PayPal::class,
//    "remita" => Remita::class,
//    "voucher" => VoucherPayment::class,
];
