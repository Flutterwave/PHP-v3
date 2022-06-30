<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\CardEventHandler;

class CardPayment
{
    protected $payment;

    function __construct() {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->valType = "card";

    }

    function cardCharge($array) {
        // echo "<pre>";
        // print_r($array);
        // echo "</pre>";
        // exit;
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        } else {
            $this->payment->txref = $array['tx_ref'];
        }

        $this->payment->type = 'card';
        //set the payment handler
        $this->payment->eventHandler(new CardEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=" . $this->payment->type);
        //returns the value from the results
        //$result = $this->payment->chargePayment($array);

        CardEventHandler::startRecording();
        $result = $this->payment->chargePayment($array);
        CardEventHandler::setResponseTime();

        return $result;

        //change this
    }

    /**you will need to validate and verify the charge
     * Validating the charge will require an otp
     * After validation then verify the charge with the txRef
     * You can write out your function to execute when the verification is successful in the onSuccessful function
     ***/

    function validateTransaction($element, $ref) {
        //validate the charge

        return $this->payment->validateTransaction($element, $ref, $this->payment->type);//Uncomment this line if you need it


    }

    function return_txref() {
        return $this->payment->txref;
    }

    function verifyTransaction($id) {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it

    }


}

