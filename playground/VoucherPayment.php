
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 



require("../library/VoucherPayment.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VoucherPayment;

//The data variable holds the payload
$data = array(
        //"public_key": "FLWPUBK-xxxxxxxxxxxxxxxxxxxxx-X"//you can ommit the public key as the key is take from your .env file
        //"tx_ref": "MC-15852309v5050e8",
        "amount"=> "100",
        "type"=> "voucher_payment",
        "currency"=> "ZAR",
        "pin"=> "19203804939000",
        "email"=>"ekene@flw.com",
        "phone_number" =>"0902620185",
        "account_bank" => "058",
        "fullname" => "Ekene Eze",
        "client_ip" =>"154.123.220.1",
        "device_fingerprint" =>"62wd23423rq324323qew1",
        "meta" => array(
            "flightID"=> "123949494DC"
        )     
    );

$payment = new VoucherPayment();
$result = $payment->voucher($data);
if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
  echo '<div class="alert alert-primary role="alert">
        <h1>Verified Result: </h1>
        <p><b> '.print_r($verify, true).'</b></p>
      </div>';
}


echo '<div class="alert alert-success role="alert">
        <h1>Charge Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';





include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


