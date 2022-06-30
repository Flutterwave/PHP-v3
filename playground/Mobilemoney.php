<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/MobileMoney.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\MobileMoney;

//The data variable holds the payload
$data = array(
    "order_id" => "USS_URG_89245453s2323",
    "amount" => "1500",
    "type" => "mobile_money_rwanda",// could be mobile_money_rwanda,mobile_money_uganda, mobile_money_zambia, mobile_money_ghana, mobile_money_franco
    "currency" => "RWF",
    "email" => "ekene@flw.com",
    "phone_number" => "054709929220",
    "fullname" => "John Madakin",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );


$payment = new MobileMoney();
$result = $payment->mobilemoney($data);

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


