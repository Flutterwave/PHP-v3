
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 



require("../library/Ussd.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Ussd;

//The data variable holds the payload
$data = array(
        "tx_ref" => "MC-15852309v5050e8",
        "account_bank" => "058",
        "amount" => "1500",
        "currency" =>"NGN",
        "email" =>"user@gmail.com",
        "phone_number" =>"054709929220",
        "fullname" => "John Madakin",
        
      
    );

$payment = new Ussd();
$result = $payment->ussd($data);//initiates the charge
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
}



echo '<div class="alert alert-success role="alert">
        <h1>Authorize Ussd Transaction: </h1>
        <p><b> Dial '.$result['meta']['authorization']['note'].'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
        <h1>Verified Result: </h1>
        <p><b> '.print_r($verify, true).'</b></p>
      </div>';






include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


