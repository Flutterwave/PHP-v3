<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');
require_once('raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class recipientEventHandler implements EventHandlerInterface{
    /**
     * This is called only when a transaction is successful
     * */
    function onSuccessful($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
       
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        // Do something, anything!
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
       
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
      
    }
}


class Recipient {
    protected $recipient;
    function __construct(){
        $this->recipient = new Rave($_ENV['SECRET_KEY']);
    }
    function createRecipient($array){
            //set the payment handler 

            if(!isset($array['account_number']) || !isset($array['account_bank'])){
                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following body params are required <b> account_number and account_bank </b>
              </div>';
            }
            $this->recipient->eventHandler(new recipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries");
            //returns the value from the results
            return $this->recipient->createBeneficiary($array);
        }

        function listRecipients(){
            $this->recipient->eventHandler(new recipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries");
            //returns the value from the results
            return $this->recipient->getBeneficiaries();
        } 

        function fetchBeneficiary($array){
            if(!isset($array['id'])){
                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following PATH param is required :<b> id </b>
              </div>';
            }

            if(gettype($array['id']) !== 'string'){
                $array['id'] = (string) $array['id'];
            }

            $this->recipient->eventHandler(new recipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries/". $array['id']);
            //returns the value from the results
            return $this->recipient->getBeneficiaries();
        }

        function deleteBeneficiary($array){

            if(!isset($array['id'])){
                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following PATH param is required :<b> id </b>
              </div>';
            }

            if(gettype($array['id']) !== 'string'){
                $array['id'] = (string) $array['id'];
            }
            
            $this->recipient->eventHandler(new recipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries/". $array['id']);
            //returns the value from the results
            return $this->recipient->deleteBeneficiary();
        }  

    }
?>