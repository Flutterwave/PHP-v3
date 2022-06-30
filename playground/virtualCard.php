
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 



require("../library/VirtualCards.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VirtualCard;

//The data variable holds the payload
$data = array(
    "currency"=>"NGN",
    "amount"=>20000,
    "billing_name"=>"Jermaine Graham",
    "billing_address"=>"2014 Forest Hills Drive",
    "billing_city"=>"Node",
    "billing_state"=>"Javascript",
    "billing_postal_code"=>"000009",
    "billing_country"=>"NG",
    "callback_url"=>"https://webhook.site/96374895-154d-4aa0-99b5-709a0a128674"
    );

    $trns_data = array('id'=> 'a41de883-c8da-45a0-9b23-37780c88285f');
    $getCardData = array('id'=>'7a81d279-a07a-4775-a55a-5fa2c98e20ae');
    $terminate_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb');
    $fund_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'amount'=>'2000', 'debit_currency'=>'NGN');
    $withdraw_data = array('id'=>'1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'amount'=>'500');
    $blockCard_data = array('id' => '1cb36826-8e05-40d6-8b9e-7f7439a141cb', 'status_action'=>'block');
$card = new VirtualCard();
$createCard = $card->createCard($data);//initiates the charge
$getCard = $card->getCard($getCardData);
$getCards = $card->listCards();
$terminate = $card->terminateCard($terminate_data);
$fund = $card->fundCard($fund_data);
$transactions = $card->cardTransactions($trns_data);
$withdraw = $card->cardWithdrawal($withdraw_data);
$block_unblock = $card->block_unblock_card($blockCard_data);



echo '<div class="alert alert-success role="alert">
        <h1>Create Card Result: </h1>
        <p><b> '.print_r($createCard, true).'</b></p>
      </div>';


echo '<div class="alert alert-primary role="alert">
        <h1>Get Card Result: </h1>
        <p><b> '.print_r($getCard, true).'</b></p>
      </div>';

echo '<div class="alert alert-primary role="alert">
      <h1>Get Cards Result: </h1>
      <p><b> '.print_r($getCards, true).'</b></p>
    </div>';

echo '<div class="alert alert-primary role="alert">
    <h1>Terminate card Result: </h1>
    <p><b> '.print_r($terminate, true).'</b></p>
  </div>';

echo '<div class="alert alert-primary role="alert">
  <h1>Fund Card Result: </h1>
  <p><b> '.print_r($fund, true).'</b></p>
</div>';

echo '<div class="alert alert-primary role="alert">
  <h1>Get Card Transactions Result: </h1>
  <p><b> '.print_r($transactions, true).'</b></p>
</div>';

echo '<div class="alert alert-primary role="alert">
  <h1> Card Withdrawal Result: </h1>
  <p><b> '.print_r($withdraw, true).'</b></p>
</div>';

echo '<div class="alert alert-primary role="alert">
  <h1>Block Card Result: </h1>
  <p><b> '.print_r($block_unblock, true).'</b></p>
</div>';










include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


