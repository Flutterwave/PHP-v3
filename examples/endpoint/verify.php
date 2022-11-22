<?php

$dev_instructions = "<h3>Sample Verify Endpoint</h3>";
$dev_instructions .= "<p>Simply make a get request to this route with either \"transactionId\" or \"tx_ref\" as the query parameter.";
###########################################################################################################################################
require __DIR__."/../../vendor/autoload.php";

$data = $_GET;

if(count($data) == 0){
    echo $dev_instructions;
}

if (isset($data['transactionId']) || isset($data['tx_ref']) )
{
    $transactionId = $data['transactionId'] ?? null;
    $tx_ref = $data['tx_ref'] ?? null;

    try {
        $transactionService = (new \Flutterwave\Service\Transactions());

        if(!is_null($transactionId)){
            $res = $transactionService->verify($transactionId);
        }else{
            $res = $transactionService->verifyWithTxref($tx_ref);
        }

        if ($res->status === 'success') {

            #TODO: get the record of the payment from your store or DB and confirm the amount and currency are the same before giving value.

            echo '<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>';
            echo "Your payment status: " . $res->data->processor_response;
            echo "<p>Note: if transaction is pending, try using the re-check button below.</p>";
            echo "<br />";
            echo "<button class='check-payment-status'> re-check </button>";
            echo "<script src='../assets/js/index.js'></script>";
        }
    } catch (\Exception $e) {

        echo "error: ". $e->getMessage();
    }

}

if(isset($data['resp']))
{
    $resp = json_decode($data['resp'], true);
    $transactionId = $resp['data']['id'] ?? null;
    $tx_ref = $resp['data']['tx_ref'] ?? null;

    try {
        $transactionService = (new \Flutterwave\Service\Transactions());

        if(!is_null($transactionId)){
            $res = $transactionService->verify($transactionId);
        }else{
            $res = $transactionService->verifyWithTxref($tx_ref);
        }

        if ($res->status === 'success') {

            #TODO: get the record of the payment from your store or DB and confirm the amount and currency are the same before giving value.

            echo '<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>';
            echo "Your payment status: " . ucfirst($res->data->status);
            echo "<p>Note: if transaction is pending, try using the re-check button below.</p>";
            echo "<br />";
            echo "<button class='check-payment-status'> re-check </button>";
            echo "<script src='../assets/js/index.js'></script>";
        }
    } catch (\Exception $e) {

        echo "error: ". $e->getMessage();
    }
}

// For Card Verification
if(isset($data['response'])){
    $resp = json_decode($data['response'], true);
    $transactionId = $resp['id'] ?? null;
    $tx_ref = $resp['txRef'] ?? null;

    try {
        $transactionService = (new \Flutterwave\Service\Transactions());

        if(!is_null($transactionId)){
            $res = $transactionService->verify($transactionId);
        }else{
            $res = $transactionService->verifyWithTxref($tx_ref);
        }

        if ($res->status === 'success') {

            #TODO: get the record of the payment from your store or DB and confirm the amount and currency are the same before giving value.

            echo '<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>';
            echo "Your payment status: " . ucfirst($res->data->status);
            echo "<p>Note: if transaction is pending, try using the re-check button below.</p>";
            echo "<br />";
            echo "<button class='check-payment-status'> re-check </button>";
            echo "<script src='../assets/js/index.js'></script>";
        }
    } catch (\Exception $e) {

        echo "error: ". $e->getMessage();
    }
}
