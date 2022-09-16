<?php

require __DIR__."/../../setup.php";

use Flutterwave\Helper;
use Flutterwave\Service;
$config = Helper\Config::getInstance(
    $_SERVER[Helper\Config::SECRET_KEY],
    $_SERVER[Helper\Config::PUBLIC_KEY],
    $_SERVER[Helper\Config::ENCRYPTION_KEY],
    $_SERVER['ENV']
);

\Flutterwave\Flutterwave::configure($config);
$transaction =  new Service\Transactions($config);

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
