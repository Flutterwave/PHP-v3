<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Service;
use Flutterwave\Util\AuthMode;

\Flutterwave\Flutterwave::bootstrap();

try {
    $ussdpayment = \Flutterwave\Flutterwave::create("ussd");

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::NGN,
        "tx_ref" => uniqid().time(),
        "redirectUrl" => null,
        "additionalData" => [
            "account_bank" => "044",
            "account_number" => "0000000000000"
        ]
    ];

    $data['redirectUrl'] = "http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php?tx_ref={$data['tx_ref']}";

    $customerObj = $ussdpayment->customer->create([
        "full_name" => "Olaobaju Jesulayomi Abraham",
        "email" => "vicomma@gmail.com",
        "phone" => "+2349067985861"
    ]);

    $data['customer'] = $customerObj;

    $payload  = $ussdpayment->payload->create($data);

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $ussdpayment->initiate($payload);

            if($result['mode'] == AuthMode::USSD){
                $instruction = $result['instruction'];
                $reference = $result["tx_ref"];
                $code = $result["code"];
                $response_display = "Dail the USSD code ";
                $response_display .= $instruction." and use the payment code <b>$code</b>";
                $response_display .= "<br />  and <br/> confirm your transaction";
                $response_display .= <<<SCRIPT
                                    <script>
                                    var reference = "$reference";
                                    </script>
                                    SCRIPT;
                $response_display .= "<br /><button class='confirm-xaf-transfer-momo'>Confirm Payment</button>";

//                header("Location: /examples/endpoint/verify.php?tx_ref=");

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
        <h3> USSD Payment Sample</h3>
        <span class="error"><?= $error ?? ""  ?></span>
        <div class="request">
            <?= $request_display ?? "" ?>
        </div>
        <div class="progress">
            <?= $response_display ?? "" ?>
        </div>
        <div class="cta">
            <button name="view" value="1">View USSD Payment Request</button>
            <button class="make-payment" name="make" value="1">Make USSD Payment</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>
