<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\AccountEventHandler;

class AccountPayment
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = array('debit_uk_account', 'debit_ng_account');
        $this->valType = "account";
    }

    function accountCharge($array)
    {
        //set the payment handler

        //add tx_ref to the paylaod
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        } else {
            $this->payment->txref = $array['tx_ref'];
        }


        if (!in_array($array['type'], $this->type)) {
            echo '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The Type specified in the payload  is not <b> "' . $this->type[0] . ' or ' . $this->type[1] . '"</b>
          </div>';
        }


        $this->payment->eventHandler(new AccountEventHandler);
        //set the endpoint for the api call
        if ($this->type === $this->type[0]) {
            $this->payment->setEndPoint("v3/charges?type=debit_uk_account");
        } else {
            $this->payment->setEndPoint("v3/charges?type=debit_ng_account");
        }

        AccountEventHandler::startRecording();
        $response = $this->payment->chargePayment($array);
        AccountEventHandler::sendAnalytics('Initiate-Account-Charge');

        return $response;
    }

    function validateTransaction($otp, $ref)
    {
        //validate the charge
        $this->payment->eventHandler(new AccountEventHandler);

        return $this->payment->validateTransaction($otp, $ref, $this->payment->type);//Uncomment this line if you need it

    }

    function return_txref()
    {
        return $this->payment->txref;
    }

    function verifyTransaction($id)
    {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it

    }
}



