<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Payload;
use Flutterwave\Service;

\Flutterwave\Flutterwave::bootstrap();

try {

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::NGN,
        "tx_ref" => uniqid().time(),
        "redirectUrl" => "https://google.com"
    ];

    $btpayment = \Flutterwave\Flutterwave::create("bank-transfer");
    $customerObj = $btpayment->customer->create([
        "full_name" => "Olaobaju Jesulayomi Abraham",
        "email" => "vicomma@gmail.com",
        "phone" => "+2349067985861"
    ]);

    $data['customer'] = $customerObj;

    $payload  = $btpayment->payload->create($data);
    $payload->set('is_permanent', 1); // for permanent;

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $btpayment->initiate($payload);
            $instruction = ($result['instruction'] !== "N/A")  ? $result['instruction'] : "Please make a Transfer Payment to the Account Below.";
            $transfer_reference = $result['transfer_reference'];
            $transfer_account = $result['transfer_account'];
            $transfer_bank = $result['transfer_bank'];
            $account_expiration = $result['account_expiration'];
            $transfer_amount = $result['transfer_amount'];
            $response_display = require __DIR__."/view/form/banktransfer.php";
        }
    }


} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<link rel="stylesheet" href="./assets/css/index.css">
<div class="buttons">
    <form method="get">
        <h3> Bank Transfer Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View Bank Transfer Request</button>
            <button class="make-payment" name="make" value="1">Make Bank Transfer Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
