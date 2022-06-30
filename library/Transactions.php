<?php
namespace Flutterwave;

use Flutterwave\EventHandlers\TransactionVerificationEventHandler;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

class Transactions{
    function __construct(){
        $this->history = new Rave($_ENV['SECRET_KEY']);
    }
    function viewTransactions(){
        //set the payment handler
        $this->history->eventHandler(new TransactionVerificationEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/transactions");
        //returns the value from the results
        TransactionVerificationEventHandler::startRecording();
        $response =  $this->history->getAllTransactions();
        TransactionVerificationEventHandler::sendAnalytics("Get-All-Transactions");

        return $response;
    }

    function getTransactionFee($array = array()){

        if(!isset($array['amount'])){
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The following query param  is required <b>  amount </b>
          </div>';
        }


        $this->history->eventHandler(new TransactionVerificationEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/transactions/fee");
        //returns the value from the results

        TransactionVerificationEventHandler::startRecording();
        $response =  $this->history->getTransactionFee($array);
        TransactionVerificationEventHandler::sendAnalytics("Get-Transaction-Fee");

        return $response;
    }

    function verifyTransaction($id){

        $this->history->eventHandler(new TransactionVerificationEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/transactions/".$id."/verify");
        //returns the value from the results

        TransactionVerificationEventHandler::startRecording();
        $response = $this->history->verifyTransaction($id);
        TransactionVerificationEventHandler::sendAnalytics("Verify-Transaction");

        return $response;
    }


    function viewTimeline($array = array()){
        if(!isset($array['id'])){
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Missing value for <b> id </b> in your payload
          </div>';
        }

        $this->history->eventHandler(new TransactionVerificationEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/transactions/".$array['id']."/events");
        //returns the value from the results

        TransactionVerificationEventHandler::startRecording();
        $response =  $this->history->transactionTimeline();
        TransactionVerificationEventHandler::sendAnalytics("View-Transaction-Timeline");

        return $response;
    }
}
