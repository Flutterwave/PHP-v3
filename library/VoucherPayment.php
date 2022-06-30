<?php

namespace Flutterwave;

use Flutterwave\EventHandlers\VoucherEventHandler;

class VoucherPayment
{
    function __construct() {
        $this->payment = new Rave($_ENV['SECRET_KEY']);

    }

    function voucher($array) {
        //add tx_ref to the paylaod
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        }

        $this->payment->type = 'voucher_payment';

        $this->payment->eventHandler(new VoucherEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=" . $this->payment->type);
        //returns the value from the results
        VoucherEventHandler::startRecording();
        $response = $this->payment->chargePayment($array);
        VoucherEventHandler::sendAnalytics("Initiate-Voucher-Payment");

        return $response;
    }

    /**you will need to verify the charge
     * After validation then verify the charge with the txRef
     * You can write out your function to execute when the verification is successful in the onSuccessful function
     ***/
    function verifyTransaction($id) {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it
    }

}



