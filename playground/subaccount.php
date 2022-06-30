<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/Subaccount.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Subaccount;

//The data variable holds the payload
$data = array(
    "account_bank"=> "044",
    "account_number"=> "0690000037",
    "business_name"=> "Eternal Blue",
    "business_email"=> "petya@stux.net",
    "business_contact"=> "Anonymous",
    "business_contact_mobile"=> "090890382",
    "business_mobile"=> "09087930450",
    "country"=> "NG",
    "meta"=> array(
        array(
            "meta_name"=> "mem_adr",
            "meta_value"=> "0x16241F327213"
        )
    ),
    "split_type"=> "percentage",
    "split_value"=> 0.5
);

$fetch_data = array(
    "id" => "RS_9247C52A37C5EB15C7E8E974CD1B35D7"
);

$update_data = array(
    "id" => "2755",
    "business_name"=>"Mad O!",
    "business_email"=> "mad@o.enterprises",
    "account_bank"=> "044",
    "account_number"=> "0690000040",
    "split_type"=> "flat",
    "split_value"=> "200"
);

$subaccount = new Subaccount();
$createSubaccount = $subaccount->createSubaccount($data);
$getSubaccounts = $subaccount->getSubaccounts();
$fetchSubaccount = $subaccount->fetchSubaccount($fetch_data);
$updateSubaccount = $subaccount->updateSubaccount($update_data);

echo '<div class="alert alert-success role="alert">
        <h1>Subaccount Creation Result: </h1>
        <p><b> '.print_r($createSubaccount, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
        <h1>[Get Subaccounts] Result: </h1>
        <p><b> '.print_r($getSubaccounts, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
      <h1>[Get Subaccounts] Result: </h1>
      <p><b> '.print_r($fetchSubaccount, true).'</b></p>
    </div>';

echo '<div class="alert alert-primary role="alert">
    <h1>[Get Subaccounts] Result: </h1>
    <p><b> '.print_r($updateSubaccount, true).'</b></p>
  </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


