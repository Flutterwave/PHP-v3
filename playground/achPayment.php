
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

DEFINE('DS', DIRECTORY_SEPARATOR);

require("../library/AchPayment.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\AchPayment;

//The data variable holds the payload



$data = array(
    "tx_ref" =>  "MC-1585230ew9v5050e8",
    "amount" => "100",
    "type" => "ach_payment",
    "currency" => "USD",
    "country" => "US",
    "email" => "ekene@gmail.com",
    "phone_number" => "0902620185",
    "fullname" => "Ekene Eze",
    "redirect_url" => "http://ekene.com/u/payment-completed",
    );

$payment = new AchPayment();

$result = $payment->achCharge($data);
echo '<div class="alert alert-success role="alert">
        <h1>Charge Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';






include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


