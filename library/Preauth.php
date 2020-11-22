<?php

namespace Flutterwave;

require("lib/rave.php");
require("lib/raveEventHandlerInterface.php");
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
        $this->preauthPayment = new Rave();
    }

    function accountCharge($array)
    {
        //set the payment handler
        $this->payment->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        $this->payment->setAuthModel("AUTH");

        preEventHandler::startRecording();
        $response = $this->payment->chargePayment($array);
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
        //set the payment handler
        $this->plan->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("flwv3-pug/getpaidx/api/capture");
        //returns the value from the results
        preEventHandler::startRecording();
        $response = $this->plan->captureFunds($array);
        preEventHandler::sendAnalytics('Initiate-Capture-Funds');

        return $response;
    }

    function refundOrVoid($array)
    {

        //set the payment handler
        $this->plan->eventHandler(new preEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("flwv3-pug/getpaidx/api/refundorvoid");
        //returns the value from the results
        preEventHandler::startRecording();
        $response = $this->plan->refundOrVoid($array);
        preEventHandler::sendAnalytics('Initiate-Refund-or-Void');

        return $response;

    }


}
