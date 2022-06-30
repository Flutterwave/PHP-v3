
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/VirtualAccount.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VirtualAccount;

//sample payload for payBill()
$data = array(
  "email"=> "johnmadakin@allstar.com",
  "duration"=> 5,
  "frequency"=> 5,
  "amount"=>"22000",
  "is_permanent"=> true,
  "tx_ref"=> "jhn-mdkn-101923123463"
);

$bulkdata = array(
  "accounts"=> 5,
  "email"=> "sam@son.com",
  "is_permanent"=> true,
  "tx_ref"=> "jhn-mndkn-012439283422"
);

$batch = array('batch_id' => 'RND_2641579516055928');

$getdata = array(
    "order_ref"=>"URF_1590362018488_8875935"
);

$account = new VirtualAccount();
$result = $account->createVirtualAccount($data);//create a virtak account
$bulkAccounts = $account->createBulkAccounts($bulkdata);//create bulk v accounts
$virtualAccounts = $account->getBulkAccounts($batch);//list all bulk accounts
$virtualAccount = $account->getAccountNumber($getdata);//get an account.

// $verify = $payment->verifyTransaction();
echo '<div class="alert alert-success role="alert">
        <h1> Virtual Account creation Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> Bulk Account creation Result: </h1>
        <p><b> '.print_r($bulkAccounts, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1> Get  Accounts Result: </h1>
      <p><b> '.print_r($virtualAccounts, true).'</b></p>
    </div>';
echo '<div class="alert alert-success role="alert">
    <h1> Get Account Result: </h1>
    <p><b> '.print_r($virtualAccount, true).'</b></p>
  </div>';



    // echo '<div class="alert alert-primary role="alert">
//         <h1>Verified Result: </h1>
//         <p><b> '.print_r($verify, true).'</b></p>
//       </div>';



include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


