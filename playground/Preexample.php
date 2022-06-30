<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 
require("../library/Preauth.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Preauth;

$payment = new Preauth();

// $result = $payment->cardCharge($card);

//$capture = $payment->captureFunds(['flw_ref' => 'flw-34552-RE', 'amount' => 2000]);//pass flw_ref and amount

// $void = $payment->voidFunds(['flw_ref' => 'flw-34552-RE']);//pass only flw_ref

//$refund = $payment->reFunds(['flw_ref' => 'flw-34552-RE', 'amount' => 2000]);//pass flw_ref and amount

echo "<pre>";
print_r($refund);
echo "<pre>";
exit;