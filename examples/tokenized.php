<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Service;
use Flutterwave\Util\AuthMode;

\Flutterwave\Flutterwave::bootstrap();

try {
    $tokenpayment = \Flutterwave\Flutterwave::create("tokenize");

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::NGN,
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

    $payload  = $tokenpayment->payload->create($data);

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $tokenpayment->initiate($payload);
            if($result['status']){
                $response_display = "Payment {$result['status']}";
            }
        }

    }


} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<link rel="stylesheet" href="./assets/css/index.css">
<div class="buttons">
    <form method="get">
        <h3> Tokenized Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View Tokenized Payment Request</button>
            <button class="make-payment" name="make" value="1">Make Tokenized Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
