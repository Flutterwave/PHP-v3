<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\UssdEventHandler;

class Ussd
{
    protected $ussd;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = "ussd";
    }

    function ussd($array)
    {

        $this->payment->type = 'ussd';

        //add tx_ref to the paylaod
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        }


        //set the payment handler
        $this->payment->eventHandler(new UssdEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=" . $this->payment->type);
        //returns the value from the results
        UssdEventHandler::startRecording();
        $response= $this->payment->chargePayment($array);
        UssdEventHandler::sendAnalytics('Initiate-USSD-Transfer');

        return $response;

    }

    function verifyTransaction($id)
    {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it
    }

}
