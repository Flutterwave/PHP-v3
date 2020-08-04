<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');
require_once('raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;



class momoEventHandler implements EventHandlerInterface{
    /**
     * This is called only when a transaction is successful 
     * @param array
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
        if($transactionData["data"]["chargecode"] === '00' || $transactionData["data"]["chargecode"] === '0'){
            echo "Transaction Completed";
        }else{
          $this->onFailure($transactionData);
      }
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

class MobileMoney {
    protected $payment;
    function __construct(){
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = array("mobile_money_ghana","mobile_money_uganda","mobile_money_zambia","mobile_money_rwanda","mobile_money_franco");
    }
    function mobilemoney($array){
         //add tx_ref to the paylaod
        //add tx_ref to the paylaod
        if(!isset($array['tx_ref']) || empty($array['tx_ref'])){
            $array['tx_ref'] = $this->payment->txref;
        }

        $this->payment->type = 'momo';
        if(!in_array($array['type'], $this->type, true)){
            echo '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The Type specified in the payload  is not <b> "'.$this->type[0].' , '.$this->type[1].' , '.$this->type[2].' , '.$this->type[3].' or '.$this->type[4].'"</b>
          </div>';
        }

            switch ($array['type']) {
                case 'mobile_money_ghana':
                    //set type to gh_momo
                    $this->type = 'mobile_money_ghana';
                    break;

                case 'mobile_money_uganda':
                    //set type to ugx_momo

                    $this->type = 'mobile_money_uganda';

                    break;

                case 'mobile_money_zambia':
                    //set type to xar_momo
                    $this->type = 'mobile_money_zambia';

                    break;
                case 'mobile_money_franco':
                    //set type to xar_momo
                    $this->type = 'mobile_money_franco';

                    break;
                
                default:
                    //set type to momo
                $this->type = 'mobile_money_rwanda';
                    
                    break;
            }
            
            //set the payment handler 
            $this->payment->eventHandler(new momoEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=".$this->type);
            //returns the value from the results
            return $this->payment->chargePayment($array);

            //echo 'Type selected: '.$this->type;

         
            
        }

         /**you will need to verify the charge
             * After validation then verify the charge with the txRef
             * You can write out your function to execute when the verification is successful in the onSuccessful function
         ***/
        function verifyTransaction($id){
            //verify the charge
            return $this->payment->verifyTransaction($id);//Uncomment this line if you need it

        }
      

    }

?>