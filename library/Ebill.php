<?php

namespace Flutterwave;

require_once('rave.php');
require_once('raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class ebillEventHandler implements EventHandlerInterface{
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

class Ebill {
    function __construct(){
        $this->eb = new Rave($_ENV['SECRET_KEY']);
        $this->keys = array('amount', 'phone_number','country', 'ip','email');
    }
    function order($array){

        if(!isset($array['tx_ref']) || empty($array['tx_ref'])){
            $array['tx_ref'] = $this->payment->txref;
        }

        if(!isset($array['amount']) || !isset($array['phone_number']) || 
        !isset($array['email']) || !isset($array['country']) || !isset($array['ip'])){
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Missing values for one of the following body params: <b> "'.$this->keys[0].' , '.$this->keys[1].' , '.$this->keys[2].' , '.$this->keys[3].' and '.$this->keys[4].'"</b>
          </div>';
        }

        
        $this->eb->eventHandler(new ebillEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/ebills");
        //returns the value of the result.
       return $this->eb->createOrder($array); 
    }

    function updateOrder($data){
        

        if(!isset($data['amount'])){
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
         Missing values for one of the following body params: <b> "'.$this->keys[0].' '.'and reference'.'"</b>
          </div>';
        }

        if(gettype($data['amount']) !== 'integer'){
            $data['amount'] = (int) $data['amount'];
        }
        

       $this->eb->eventHandler(new ebillEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("v3/ebills/".$data['reference']);
        //returns the value of the result.
       return $this->eb->updateOrder($data); 
    }
}





