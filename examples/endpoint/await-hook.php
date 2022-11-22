<?php

require __DIR__."/../../vendor/autoload.php";

use Flutterwave\Helper;
use Flutterwave\Service;

$transaction =  new Service\Transactions();

echo "Confirming Payment...";

$body = @file_get_contents("php://input");
$body = json_decode($body, true) ?? [];
$verified = 0;
while(!array_key_exists('status', $body)){
    $id = $body['transactionId']?? "000000000";
    try {
        $body = (array)$transaction->verify($id)->data;
        $verified = 1;
    } catch (\Exception $e) {
//        echo "error: {$e->getMessage()}";
    }
}

if(is_array($body) && !empty($body))
{
    echo "Payment Status: Successful";
}
