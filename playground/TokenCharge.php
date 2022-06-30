
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 



require("../library/TokenizedCharge.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\TokenizedCharge;

//The data variable holds the payload
$data = array(
     "token"=> "flw-t1nf-1ff187b04cecb4acff4ac62c2b6f7784-m03k",
     "currency"=> "NGN",
     "country"=> "NG",
     "amount"=> 30300,
     "email"=> "olaobajua@gmail.com",
     "first_name"=> "Anonymous",
     "last_name"=> "customer",
     "client_ip" =>"154.123.220.1",
     "device_fingerprint" =>"62wd23423rq324323qew1" 
    );

$payment = new TokenizedCharge();
$result = $payment->tokenCharge($data);//initiates the charge
$verify = $payment->verifyTransaction();


echo '<div class="alert alert-success role="alert">
        <h1>Charge Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
        <h1>Verified Result: </h1>
        <p><b> '.print_r($verify, true).'</b></p>
      </div>';






include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


