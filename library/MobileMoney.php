<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\MomoEventHandler;

class MobileMoney
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = array("mobile_money_ghana", "mobile_money_uganda", "mobile_money_zambia", "mobile_money_rwanda", "mobile_money_franco");
    }

    function mobilemoney($array)
    {
        //add tx_ref to the paylaod
        //add tx_ref to the paylaod
        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        }

        $this->payment->type = 'momo';
        if (!in_array($array['type'], $this->type, true)) {
            echo '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The Type specified in the payload  is not <b> "' . $this->type[0] . ' , ' . $this->type[1] . ' , ' . $this->type[2] . ' , ' . $this->type[3] . ' or ' . $this->type[4] . '"</b>
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
        $this->payment->eventHandler(new MomoEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=" . $this->type);
        //returns the value from the results

        MomoEventHandler::startRecording();
        $response = $this->payment->chargePayment($array);

        MomoEventHandler::setResponseTime();

        return $response;
        //echo 'Type selected: '.$this->type;


    }

    /**you will need to verify the charge
     * After validation then verify the charge with the txRef
     * You can write out your function to execute when the verification is successful in the onSuccessful function
     ***/
    function verifyTransaction($id)
    {
        //verify the charge
        return $this->payment->verifyTransaction($id);//Uncomment this line if you need it

    }


}
