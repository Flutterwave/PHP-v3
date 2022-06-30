<?php

namespace Flutterwave;

use Flutterwave\EventHandlers\EbillEventHandler;

class Ebill
{
    function __construct() {
        $this->eb = new Rave($_ENV['SECRET_KEY']);
        $this->keys = array('amount', 'phone_number', 'country', 'ip', 'email');
    }

    function order($array) {

        if (empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->payment->txref;
        }

        if (!isset($array['amount']) || !isset($array['phone_number']) ||
            !isset($array['email']) || !isset($array['country']) || !isset($array['ip'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Missing values for one of the following body params: <b> "' . $this->keys[0] . ' ,
             ' . $this->keys[1] . ' , ' . $this->keys[2] . ' , ' . $this->keys[3] . ' and ' . $this->keys[4] . '"</b>
          </div>';
        }


        $this->eb->eventHandler(new EbillEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/ebills");
        //returns the value of the result.
        return $this->eb->createOrder($array);
    }

    function updateOrder($data) {


        if (!isset($data['amount'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
         Missing values for one of the following body params: <b> "' . $this->keys[0] . ' ' . 'and reference' . '"</b>
          </div>';
        }

        if (gettype($data['amount']) !== 'integer') {
            $data['amount'] = (int)$data['amount'];
        }


        $this->eb->eventHandler(new EbillEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/ebills/" . $data['reference']);
        //returns the value of the result.
        return $this->eb->updateOrder($data);
    }
}





