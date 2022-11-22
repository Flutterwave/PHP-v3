<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Service;
use Flutterwave\Util\AuthMode;

\Flutterwave\Flutterwave::bootstrap();

try {
    $momopayment = \Flutterwave\Flutterwave::create("momo");

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::ZMW,
        "tx_ref" => uniqid().time(),
        "redirectUrl" => null,
        "additionalData" => [
            "network" => "MTN",
        ]
    ];

    $data['redirectUrl'] = "http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php?tx_ref={$data['tx_ref']}";

    $customerObj = $momopayment->customer->create([
        "full_name" => "Olaobaju Jesulayomi Abraham",
        "email" => "vicomma@gmail.com",
        "phone" => "+2349067985861"
    ]);

    $data['customer'] = $customerObj;

    $payload  = $momopayment->payload->create($data);

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $momopayment->initiate($payload);
//            print_r($result);

            if($result['mode'] == AuthMode::CALLBACK){
                $instruction = $result['instruction'];

                $reference = $data["tx_ref"];

                $response_display = "";
                $response_display .= $instruction;
                $response_display .= "<br />  and <br/> confirm your transaction";
                $response_display .= <<<SCRIPT
                                    <script>
                                    var reference = "$reference";
                                    </script>
                                    SCRIPT;
                $response_display .= "<br /><button class='confirm-xaf-transfer-momo'>Confirm Payment</button>";

            }

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
        <h3> Momo Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View Momo Payment Request</button>
            <button class="make-payment" name="make" value="1">Make Momo Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
