<?php

$dev_instructions = "<h3>Sample Validate Endpoint</h3>";
$dev_instructions .= "<p>Simply make a post request to this route with both \"otp\" and \"flw_ref\" as the request body.";

###################################################################################################################################
require __DIR__."/../../vendor/autoload.php";

$data = $_POST;

if(count($data) == 0){
    echo $dev_instructions;
}

if (isset($data['otp']) && isset($data['flw_ref']))
{
    $otp = $data['otp'];
    $flw_ref = $data['flw_ref'];

    try {
        $res = (\Flutterwave\Service\Transactions::validate($otp, $flw_ref));

        if ($res->status === 'success') {
            echo "Your payment status: " . $res->processor_response;
        }
    } catch (\Unirest\Exception $e) {

        echo "error: ". $e->getMessage();
    }

}