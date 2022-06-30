<?php
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 




require("../library/Recipient.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Recipient;

//sample payload for payBill()
$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000036",
);
$fetchdata = array(
  'id' => '6153'
);
$deldata = array(
  'id'=>'7236'
);

$payment = new Recipient();
$recipient1 = $payment->createRecipient($data);//Create a recipient for transfer
$recipients = $payment->listRecipients();// get all existing recipients
$recipient = $payment->fetchBeneficiary($fetchdata);//fetch a specific recipient.
$deleteRecipient = $payment->deleteBeneficiary($deldata);//delete recipient

echo '<div class="alert alert-success role="alert">
        <h1> Create Recipient Result: </h1>
        <p><b> '.print_r($recipient1, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> [GET Recipients] Result: </h1>
        <p><b> '.print_r($recipients, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1>  [GET a Recipient] Result : </h1>
      <p><b> '.print_r($recipient, true).'</b></p>
    </div>';

echo '<div class="alert alert-success role="alert">
    <h1> Successful [Delete Recipient] : </h1>
    <p><b> '.print_r($deleteRecipient, true).'</b></p>
  </div>';







include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


