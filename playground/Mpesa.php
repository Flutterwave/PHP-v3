
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 



require("../library/Mpesa.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Mpesa;

//The data variable holds the payload
$data = array(
    "amount" => "1500",
    "type" => "mpesa",
    "currency" => "KES",
    "email" => "ekene@flw.com",
    "phone_number" => "054709929220",
    "fullname" => "Ekene Eze",
    "client_ip" => "154.123.220.1",
    "device_fingerprint" => "62wd23423rq324323qew1",
    "meta" => [
        "flightID" => "213213AS"
        ]       
    );

$payment = new Mpesa();

$result = $payment->mpesa($data);

echo '<div class="alert alert-success role="alert">
        <h1>Charge Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

if(isset($result['data'])){
  $id = $result['data']['id'];
  $verify = $payment->verifyTransaction($id);
  echo '<div class="alert alert-primary role="alert">
        <h1>Verified Result: </h1>
        <p><b> '.print_r($verify, true).'</b></p>
      </div>';
}







include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


