<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php


use Flutterwave\EventHandlers\TransactionVerificationEventHandler;

class TransactionVerification
{
    protected $validate;

    function __construct()
    {
        $this->validate = new Rave($_ENV['SECRET_KEY']);
    }

    function transactionVerify($id)
    {
        //set the payment handler
        $this->validate->eventHandler(new TransactionVerificationEventHandler);
        //returns the value from the results
        TransactionVerificationEventHandler::startRecording();
        $response = $this->validate->verifyTransaction($id);
        TransactionVerificationEventHandler::sendAnalytics('Verify-Transaction');

        return $response;
    }
}

?>
