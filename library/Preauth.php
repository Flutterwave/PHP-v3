<?php

namespace Flutterwave;

require("lib/rave.php");
require("lib/raveEventHandlerInterface.php");

use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

class preEventHandler implements EventHandlerInterface{

}

class Preauth {
    function __construct(){
        $this->preauthPayment =  new Rave();
    }

    function accountCharge($array){
        //set the payment handler 
        $this->payment->eventHandler(new accountEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("");
        //returns the value from the results
        //you can choose to store the returned value in a variable and validate within this function
        $this->payment->setAuthModel("AUTH");
        return $this->payment->chargePayment($array);
        /**you will need to validate and verify the charge
         * Validating the charge will require an otp
         * After validation then verify the charge with the txRef
         * You can write out your function to execute when the verification is successful in the onSuccessful function
         ***/
    }

    function captureFunds($array){
        //set the payment handler 
        $this->plan->eventHandler(new preEventHandler)
        //set the endpoint for the api call
        ->setEndPoint("flwv3-pug/getpaidx/api/capture");
        //returns the value from the results
        return $this->plan->captureFunds($array);
    }

    function refundOrVoid($array){

         //set the payment handler 
         $this->plan->eventHandler(new preEventHandler)
         //set the endpoint for the api call
         ->setEndPoint("flwv3-pug/getpaidx/api/refundorvoid");
         //returns the value from the results
         return $this->plan->refundOrVoid($array);

    }


}
