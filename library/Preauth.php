<?php

namespace Flutterwave;

require("rave.php");
require("raveEventHandlerInterface.php");
require_once('EventTracker.php');

class preEventHandler implements EventHandlerInterface
{

    use EventTracker;

    function onSuccessful($transactionData)
    {
        self::sendAnalytics("Initiate-Preauth");
    }

    function onFailure($transactionData)
    {
        self::sendAnalytics("Initiate-Preauth-Error");
    }

    function onRequery($transactionReference)
    {
        // TODO: Implement onRequery() method.
    }

    function onRequeryError($requeryResponse)
    {
        // TODO: Implement onRequeryError() method.
    }

    function onCancel($transactionReference)
    {
        // TODO: Implement onCancel() method.
    }

    function onTimeout($transactionReference, $data)
    {
        // TODO: Implement onTimeout() method.
    }
}

class Preauth
{
    function __construct()
    {
        $this->preauthPayment = new Rave($_ENV['SECRET_KEY']);
    }

    function cardCharge($array)
    {
        // echo "<pre>";
        // print_r($array);
        // echo "</pre>";
        // exit;
        $mode = "";
        if(!isset($array['preauthorize'])|| empty($array['preauthorize']))
        {
            $array['preauthorize'] = true;
        }

        if(isset($array['usesecureauth']) && $array['usesecureauth'])
        {
            $mode = "VBVSECURECODE";
        }

        if (!isset($array['tx_ref']) || empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->preauthPayment->txref;
        } else {
            $this->preauthPayment->txref = $array['tx_ref'];

        }

        $this->preauthPayment->type = 'card';
        //set the payment handler
        $this->preauthPayment->eventHandler(new preEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/charges?type=" . $this->preauthPayment->type);
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        $this->preauthPayment->setAuthModel("AUTH");

        preEventHandler::startRecording();
        $response = $this->preauthPayment->chargePayment($array);
        preEventHandler::sendAnalytics('Initiate-Preauth');

        return $response;
        /**you will need to validate and verify the charge
         * Validating the charge will require an otp
         * After validation then verify the charge with the txRef
         * You can write out your function to execute when the verification is successful in the onSuccessful function
         ***/
    }

    function captureFunds($array)
    {

        if(isset($array['flw_ref']) && !empty($array['flw_ref'])){
            $flw_ref = $array['flw_ref'];
        }else{
            $result['message'] = "Please pass the transaction flw_ref ";
            return '<div class="alert alert-danger text-center">' . $result['message'] . '</div>';;
        }
        //set the payment handler
        $this->preauthPayment->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges/$flw_ref/capture");
        //returns the value from the results
        preEventHandler::startRecording();
        $response = $this->preauthPayment->captureFunds($array);
        preEventHandler::sendAnalytics('Initiate-Capture-Funds');

        return json_decode($response);
    }

    function voidFunds($array)
    {
        if(isset($array['flw_ref']) && !empty($array['flw_ref'])){
            $flw_ref = $array['flw_ref'];
        }else{
            $result['message'] = "Please pass the transaction flw_ref ";
            return '<div class="alert alert-danger text-center">' . $result['message'] . '</div>';;
        }
        //set the payment handler
        $this->preauthPayment->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges/$flw_ref/void");
        //returns the value from the results
        preEventHandler::startRecording();
        $response = $this->preauthPayment->void($array);
        preEventHandler::sendAnalytics('Initiate-Preauth-Void');

        return json_decode($response);

    }

    function reFunds($array)
    {
        if(isset($array['flw_ref']) && !empty($array['flw_ref'])){
            $flw_ref = $array['flw_ref'];
        }else{
            $result['message'] = "Please pass the transaction flw_ref ";
            return '<div class="alert alert-danger text-center">' . $result['message'] . '</div>';;
        }
        //set the payment handler
        $this->preauthPayment->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges/$flw_ref/refund");
        //returns the value from the results
        preEventHandler::startRecording();
        $response = $this->preauthPayment->preRefund($array);
        preEventHandler::sendAnalytics('Initiate-Preauth-Refund');

        return json_decode($response);

    }


}
