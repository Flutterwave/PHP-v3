
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

DEFINE('DS', DIRECTORY_SEPARATOR);

require("../library/AccountPayment.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\AccountPayment;

//The data variable holds the payload



$data = array(
    "amount" => "3000",
    "type" => "debit_ng_account",
    "account_bank" => "044",
    "account_number" => "0690000037",
    "currency" => "NGN",
    "email" => "olaobajua@gmail.com",
    "phone_number" => "07067965809",
    "fullname" => "Olaobaju Abraham",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );

$payment = new AccountPayment();

$result = $payment->accountCharge($data);
$sera = serialize($payment);

$filePath = getcwd().DS."account.txt";
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
echo '<iframe src = "otp.php?ref='.$result['data']['flw_ref'].'&id='.$result['data']['id'].'" width = "100%" height = "400" frameBorder="0">
Sorry your browser does not support inline frames.
</iframe>';





include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


