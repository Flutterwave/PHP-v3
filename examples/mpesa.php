<?php
require __DIR__."/../vendor/autoload.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Service;

\Flutterwave\Flutterwave::bootstrap();

try {
    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::KES,
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

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $mpesapayment->initiate($payload);
            $instruction = $result['instruction'];
            $transactionId = $result['transactionId'];
            $response_display = require __DIR__."/view/form/mpesa.php";
        }

    }


} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<link rel="stylesheet" href="./assets/css/index.css">
<div class="buttons">
    <form method="get">
        <h3> Mpesa Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View Mpesa Payment Request</button>
            <button class="make-payment" name="make" value="1">Make Mpesa Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
