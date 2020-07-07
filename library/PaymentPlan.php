<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');
require_once('raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class paymentPlanEventHandler implements EventHandlerInterface{
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


class PaymentPlan{
    protected $plan;
    function __construct(){
        $this->plan = new Rave($_ENV['SECRET_KEY']);
    }
    function createPlan($array){
            //set the payment handler 
            $this->plan->eventHandler(new paymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans");

            if(empty($array['amount']) || !array_key_exists('amount',$array) || 
            empty($array['name']) || !array_key_exists('name',$array) || 
            empty($array['interval']) || !array_key_exists('interval',$array) ||
            empty($array['duration']) || !array_key_exists('duration',$array)){

                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for the following parameters  <b> amount, name , interval, or duration </b>
              </div>';
            }

            // if(!empty($array['amount'])){

            // }
            
            //returns the value from the results
            return $this->plan->createPlan($array);

        }

        function updatePlan($array){

            if(!isset($array['id']) || !isset($array['name']) || !isset($array['status'])){
                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for a parametter: <b> id, name, or status</b>
              </div>';
            }

            //set the payment handler 
            $this->plan->eventHandler(new paymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/".$array['id']);


            return $this->plan->updatePlan($array);

        }

        function cancelPlan($array){

            if(!isset($array['id'])){
                return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for a parametter: <b> id</b>
              </div>';
            }

            //set the payment handler 
            $this->plan->eventHandler(new paymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/".$array['id']."/cancel");

            return $this->plan->cancelPlan($array);

        }

        function getPlans(){
            //set the payment handler 
            $this->plan->eventHandler(new paymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans");

            return $this->plan->getPlans();
        }

        function get_a_Plan($array){
            //set the payment handler 
            $this->plan->eventHandler(new paymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/".$array['id']);

             return $this->plan->get_a_Plan();
        }
    }
?>