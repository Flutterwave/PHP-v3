<?php
require __DIR__."/../vendor/autoload.php";
require "../setup.php";

session_start();

use Flutterwave\Helper;
use Flutterwave\Payload;
use Flutterwave\Service;
use Flutterwave\Util\AuthMode;

\Flutterwave\Flutterwave::bootstrap();

try {
    $preauthpayment = \Flutterwave\Flutterwave::create("preauth");

    $data = [
        "amount" => 2000,
        "currency" => Flutterwave\Util\Currency::NGN,
        "tx_ref" => uniqid().time(),
        "redirectUrl" => null,
        "additionalData" => [
            "subaccounts" => [
                ["id" => "RSA_345983858845935893"]
            ],
            "meta" => [
                "unique_id" => uniqid().uniqid()
            ],
            "payment_plan" => null,
            "card_details" => [
                "card_number" => "5531886652142950",
                "cvv" => "564",
                "expiry_month" => "09",
                "expiry_year" => "32"
            ]
        ],
    ];

    $data['redirectUrl'] = "http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php?tx_ref={$data['tx_ref']}";

    $customerObj = $preauthpayment->customer->create([
        "full_name" => "Jack Logan Hugh",
        "email" => "Jhughck@gmail.com",
        "phone" => "+2349062919861"
    ]);

    $data['customer'] = $customerObj;

    $payload  = $preauthpayment->payload->create($data);

    if(!empty($_REQUEST))
    {
        $request = $_REQUEST;

        if(isset($request['view'])){
            $json = json_encode($payload->toArray());
            $request_display = $json;
        }

        if(isset($request['make'])){
            $result = $preauthpayment->initiate($payload);
            $instruction = $result['instruction'];
            require __DIR__."/view/form/pin.php";
        }

        if(isset($request[Payload::PIN])){
            $payload->set(Payload::PIN,$request['pin']);
            $result = $preauthpayment->initiate($payload);

            switch ($result['mode']){
                case 'redirect':
                    header("Location:".$result['url']);
                    break;
                case 'otp':
                    require __DIR__."/view/form/otp.php";
                    break;
            }

        }

    }


} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<link rel="stylesheet" href="./assets/css/index.css">
<?php if(!isset($_GET['make'])):?>
    <div class="buttons">
        <form method="get">
            <h3> Preauth Payment Sample</h3>
            <span class="error"><?= $error ?? ""  ?></span>
            <div class="request">
                <?= $request_display ?? "" ?>
            </div>
            <div class="progress">
                <?= $response_display ?? "" ?>
            </div>
            <div class="cta">
                <button name="view" value="1">View Preauth Payment Request</button>
                <button class="make-payment" name="make" value="1">Make Preath Payment</button>
            </div>
        </form>
    </div>
<?php endif ?>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="./assets/js/index.js"></script>