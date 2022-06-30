
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/PaymentPlan.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\PaymentPlan;

//sample payload for payBill()
$data = array(
    "amount"=> 2000,
    "name"=> "plan 2",
    "interval"=> "monthly",
    "duration"=> 48
);

$update = array(
    "id" => "5356",
    "name" => "The Game",
    "status" => "Active"
);

$getdata = array(
    "id"=>"5116"
);

$payment = new PaymentPlan();
$result = $payment->createPlan($data);//create a Plan reciept
$updateResult = $payment->updatePlan($update);//update a plan....
$paymentPlans = $payment->getPlans();//list all payment plans....
$aPlan = $payment->get_a_plan($getdata);//get a payment plans....

// $verify = $payment->verifyTransaction();
echo '<div class="alert alert-success role="alert">
        <h1> Successful PaymentPlan creation Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> PaymentPlan Update Result: </h1>
        <p><b> '.print_r($updateResult, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1> PaymentPlan Update Result: </h1>
      <p><b> '.print_r($paymentPlans, true).'</b></p>
    </div>';
echo '<div class="alert alert-success role="alert">
    <h1> PaymentPlan Update Result: </h1>
    <p><b> '.print_r($aPlan, true).'</b></p>
  </div>';



    // echo '<div class="alert alert-primary role="alert">
//         <h1>Verified Result: </h1>
//         <p><b> '.print_r($verify, true).'</b></p>
//       </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


