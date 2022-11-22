<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Util\AuthMode;
\Flutterwave\Flutterwave::bootstrap();

try {

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::NGN,
        "tx_ref" => uniqid().time(),
        "redirectUrl" => "https://google.com"
    ];

    $data['redirectUrl'] = "http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php?tx_ref={$data['tx_ref']}";

    $applepayment = \Flutterwave\Flutterwave::create("apple");
    $customerObj = $applepayment->customer->create([
        "full_name" => "Olaobaju Jesulayomi Abraham",
        "email" => "vicomma@gmail.com",
        "phone" => "+2349067985861"
    ]);

    $data['customer'] = $customerObj;

    $payload  = $applepayment->payload->create($data);

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $applepayment->initiate($payload);
//            print_r($result);

            if($result['mode'] == AuthMode::REDIRECT){
                header("Location: ".$result['url']);
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
        <h3> ApplePay Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View Apple Payment Request</button>
            <button class="make-payment" name="make" value="1">Make Apple Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
