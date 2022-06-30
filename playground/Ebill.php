
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/Ebill.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Ebill;

//sample payload for payBill()
$data = array(
    "narration"=> "mndkn blls",
    "number_of_units"=> 2,//should be a string
    "currency"=> "NGN",
    "amount"=> 200,//shoould be a string
    "phone_number"=> "09384747474",
    "email"=>"jake@rad.com",
    "tx_ref"=> "akhlm-pstmn-1094434370393",
    "ip"=> "127.9.0.7",
    "custom_business_name"=> "John Madakin",
    "country"=> "NG"
);

$update = array(
    "reference"=>"RVEBLS-2B93A7039017-90937",//on creation of order, this is the flw_ref
    "currency"=> "NGN",
    "amount"=> "4000"
);

$payment = new Ebill();
$result = $payment->order($data);//create an order reciept
$updateResult = $payment->updateOrder($update);//create bulk bill payment....

// $verify = $payment->verifyTransaction();
echo '<div class="alert alert-success role="alert">
        <h1> Successful Ebill creation Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> Order Update Result: </h1>
        <p><b> '.print_r($updateResult, true).'</b></p>
      </div>';



    // echo '<div class="alert alert-primary role="alert">
//         <h1>Verified Result: </h1>
//         <p><b> '.print_r($verify, true).'</b></p>
//       </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


