
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

DEFINE('DS', DIRECTORY_SEPARATOR);

require("../library/CardPayment.php");
use Flutterwave\Card;
//The data variable holds the payload
$data = array(
    "card_number"=> "5531886652142950",
            "cvv"=> "564",
            "expiry_month"=> "09",
            "expiry_year"=> "22",
            "currency"=> "NGN",
            "amount"=> "1000",
            "fullname"=> "Ekene Eze",
            "email"=> "ekene@flw.com",
            "phone_number"=> "0902620185",
            "fullname"=> "temi desola",
            "tx_ref"=> "MC-3243e",// should be unique for every transaction
            "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
            "authorization"=> [
                "mode"=> "pin",
                "pin"=> "3310",
                "country"=> "NG"
            ]
    );

$payment = new Card();

$result = $payment->cardCharge($data);
$sera = serialize($payment);

$filePath = getcwd().DS."payment.txt";
if (is_writable($filePath)) {
    $fp = fopen($filePath, "w"); 
    fwrite($fp, $sera); 
    fclose($fp);
}

echo '<div class="alert alert-success role="alert">
        <h1>Charge Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';



//validating the charge by Entering otp.....
echo '<iframe src = "otp2.php?ref='.$result['data']['flw_ref'].'" width = "100%" height = "400" frameBorder="0">
Sorry your browser does not support inline frames.
</iframe>';





include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


