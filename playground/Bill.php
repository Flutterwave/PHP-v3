
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/Bill.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Bill;

//sample payload for payBill()
$data = array(
    "country"=> "NG",
	"customer"=> "+23490803840303",
	"amount"=> 500,
	"recurrence"=> "ONCE",
	"type"=> "AIRTIME",
	"reference"=> "9300049645534545454332433"
);

//sample payload for bulkBill()
$bulkdata = array(
    "bulk_reference"=>"edf-12de5223d2f3243474543",
    "callback_url"=>"https://webhook.site/96374895-154d-4aa0-99b5-709a0a128674",
    "bulk_data"=> array(
        array(
        "country"=> "NG",
        "customer"=> "+23490803840303",
        "amount"=> 500,
        "recurrence"=> "WEEKLY",
        "type"=> "AIRTIME",
        "reference"=>"930049200929"
        ),
        array(
        "country"=>"NG",
        "customer"=> "+23490803840304",
        "amount"=> 500,
        "recurrence"=> "WEEKLY",
        "type"=>"AIRTIME",
        "reference"=>"930004912332434232"
        )
    ),
);

$getdata = array(
    //"reference"=>"edf-12de5223d2f32434753432"
     "id"=>"BIL136",
     "product_id"=>"OT150"
);

$payment = new Bill();
$result = $payment->payBill($data);//create a bill paymenr
$bulkresult = $payment->bulkBill($bulkdata);//create bulk bill payment....
$getresult = $payment->getBill($getdata);// get bulk result....
$getAgencies = $payment->getAgencies();
$getBillCategories = $payment->getBillCategories();
// $verify = $payment->verifyTransaction();
echo '<div class="alert alert-success role="alert">
        <h1> Successful Bill creation Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> Successful Bulk Bill creation Result: </h1>
        <p><b> '.print_r($bulkresult, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1> Successful [GET] Billing Result: </h1>
      <p><b> '.print_r($getresult, true).'</b></p>
    </div>';

echo '<div class="alert alert-success role="alert">
    <h1> Successful [GET Agencies] Billing Result: </h1>
    <p><b> '.print_r($getAgencies, true).'</b></p>
  </div>';

echo '<div class="alert alert-success role="alert">
  <h1> Successful [GET bill categories] Billing Result: </h1>
  <p><b> '.print_r($getBillCategories, true).'</b></p>
</div>';

    // echo '<div class="alert alert-primary role="alert">
//         <h1>Verified Result: </h1>
//         <p><b> '.print_r($verify, true).'</b></p>
//       </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


