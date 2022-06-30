<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 




require("../library/Transfer.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Transfer;

//sample payload for payBill()
$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000040",
    "amount"=> 5500,
    "narration"=> "Akhlm Pstmn Trnsfr xx007",
    "currency"=> "NGN",
    "reference"=> "akhlm-pstmnpyt-rfxx007_PMCKDU_1",// read the docs about testing successful and failed transaction.
    "callback_url"=> "https://webhook.site/b3e505b0-fe02-430e-a538-22bbbce8ce0d",
    "debit_currency"=> "NGN"
);

//sample payload for bulkBill()
$bulkdata = array(
  "title"=> "Staff salary",
  "bulk_data"=> array(
      array(
          "bank_code"=> "044",
          "account_number"=> "0690000032",
          "amount"=> 45000,
          "currency"=> "NGN",
          "narration"=> "akhlm blktrnsfr",
          "reference"=> "akhlm-blktrnsfr-xx03"
      ),
      array(
          "bank_code"=> "044",
          "account_number"=> "0690000034",
          "amount"=> 5000,
          "currency"=> "NGN",
          "narration"=> "akhlm blktrnsfr",
          "reference"=> "akhlm-blktrnsfr-xy03"
      ))
);

$getdata = array(
    //"reference"=>"edf-12de5223d2f32434753432"
     "id"=>"BIL136",
     "product_id"=>"OT150"
);

$listdata = array(
  'status'=>'failed'
);

$feedata = array(
'currency'=> 'NGN', //if currency is omitted. the default currency of NGN would be used.
'amount'=> 1000
);

$payment = new Transfer();
$result = $payment->singleTransfer($data);//initiate single transfer payment
$createBulkTransfer = $payment->bulkTransfer($bulkdata);// get bulk result....
$transfers = $payment->listTransfers($listdata);//you can add a payload for the page. you can remove the array if want to get it all.
$getTransferFee = $payment->getTransferFee($feedata);
$verify = $payment->verifyTransaction();
echo '<div class="alert alert-success role="alert">
        <h1> Transfer Initiation Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> Bulk Transfer creation Result: </h1>
        <p><b> '.print_r($createBulkTransfer, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1> Successful [GET Transfer History : </h1>
      <p><b> '.print_r($transfers, true).'</b></p>
    </div>';



echo '<div class="alert alert-success role="alert">
  <h1> Successful [GET bill categories] Billing Result: </h1>
  <p><b> '.print_r($getTransferFee, true).'</b></p>
</div>';

    echo '<div class="alert alert-primary role="alert">
        <h1>Verified Result: </h1>
        <p><b> '.print_r($verify, true).'</b></p>
      </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


