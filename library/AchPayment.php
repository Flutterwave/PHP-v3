<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\AchEventHandler;


class AchPayment
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);


    }

    function achCharge($array)
    {

        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        } else {
            $this->payment->txref = $array['tx_ref'];
        }

        $this->payment->type = 'ach_payment';
        //set the payment handler
        $this->payment->eventHandler(new AchEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=" . $this->payment->type);
        //returns the value from the results
        //$result = $this->payment->chargePayment($array);

        AchEventHandler::startRecording();
        $result = $this->payment->chargePayment($array);
        AchEventHandler::sendAnalytics('Initiate-Ach-Payment');
        return $result;

        //change this
    }


    function verifyTransaction($id)
    {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it

    }


}
