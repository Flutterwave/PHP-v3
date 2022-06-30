<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/Transactions.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Transactions;

//The data variable holds the payload
$data = array(
'amount'=> 1000
);
$fetch_data = array(
'id'=>'345522'
);
$time_data = array(
  'id'=>'3434'
);

$history = new Transactions();
$transactions = $history->viewTransactions();
$transactionfee = $history->getTransactionFee($data);
$verifyTransaction = $history->verifyTransaction($fetch_data);
$timeline = $history->viewTimeline($time_data);

echo '<div class="alert alert-success role="alert">
        <h1> Get Transactions Result: </h1>
        <p><b> '.print_r($transactions, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
        <h1>[Get transaction fee] Result: </h1>
        <p><b> '.print_r($transactionfee, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
      <h1>[Verify Transaction] Result: </h1>
      <p><b> '.print_r($verifyTransaction, true).'</b></p>
    </div>';

echo '<div class="alert alert-primary role="alert">
    <h1>[Get Timeline] Result: </h1>
    <p><b> '.print_r($timeline, true).'</b></p>
  </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


