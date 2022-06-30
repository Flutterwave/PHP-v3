<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php


use Flutterwave\EventHandlers\TkEventHandler;

class TokenizedCharge
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
    }

    function tokenCharge($array)
    {

        //add tx_ref to the paylaod
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        }

        if (gettype($array['amount']) !== "integer") {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Amount needs to be an integer.
          </div>';
        }

        if (!isset($array['token']) || !isset($array['currency']) || !isset($array['country']) ||
            !isset($array['amount']) || !isset($array['email'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Missing Param in the Payload. Please check you payload.
          </div>';
        }
        //set the payment handler
        $this->payment->eventHandler(new TkEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/tokenized-charges");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        TkEventHandler::startRecording();
        $response = $this->payment->tokenCharge($array);
        TkEventHandler::sendAnalytics('Initiate-Token-charge');

        return $response;
    }


    function updateEmailTiedToToken($data)
    {

        //set the payment handler
        $this->payment->eventHandler(new TkEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v2/gpx/tokens/embed_token/update_customer");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        TkEventHandler::startRecording();
        $response = $this->payment->postURL($data);
        TkEventHandler::sendAnalytics('Update-Email-tied-to-Token');

        return $response;

    }

    function bulkCharge($data)
    {
        //https://api.ravepay.co/flwv3-pug/getpaidx/api/tokenized/charge_bulk
        //set the payment handler
        $this->payment->eventHandler(new TkEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("flwv3-pug/getpaidx/api/tokenized/charge_bulk");

        TkEventHandler::startRecording();
        $response = $this->payment->bulkCharges($data);
        TkEventHandler::sendAnalytics('Initiate-Tokenized-Bulk-charge');

        return $response;

    }

    function bulkChargeStatus($data)
    {
        //https://api.ravepay.co/flwv3-pug/getpaidx/api/tokenized/charge_bulk
        //set the payment handler
        $this->payment->eventHandler(new TkEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("flwv3-pug/getpaidx/api/tokenized/charge_bulk");

//        tkEventHandler::startRecording();
        $response = $this->payment->bulkCharges($data);
//        tkEventHandler::sendAnalytics('Get-Tokenized-Bulk-charge-status');

        return $response;
    }

    function verifyTransaction()
    {
        //verify the charge
        return $this->payment->verifyTransaction($this->payment->txref);//Uncomment this line if you need it
    }


}

