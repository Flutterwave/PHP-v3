<?php
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 




require("../library/Subscription.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Subscription;

//sample payload for payBill()
$id = 1112; //Id of subscription plan
$cid = 2222;
$subscription = new Subscription();
$subscriptions = $subscription->getAllSubscription();//gets all existing subscription
$resultActivate = $subscription->activateSubscription($id);// activates a subscription plan
$resultCancel = $subscription->cancelSubscription($cid);// activates a subscription plan

echo '<div class="alert alert-success role="alert">
        <h1> All Subscriptions Result: </h1>
        <p><b> '.print_r($subscriptions, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
        <h1> Activate Subscription Result: </h1>
        <p><b> '.print_r($resultActivate, true).'</b></p>
      </div>';

echo '<div class="alert alert-success role="alert">
      <h1>  Cancel Subscription Result : </h1>
      <p><b> '.print_r($resultCancel, true).'</b></p>
    </div>';









include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>





















