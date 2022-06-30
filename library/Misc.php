<?php

namespace Flutterwave;

use Unirest\Request;
use Unirest\Request\Body;

class Misc
{
    function __construct()
    {
        $this->misc = new Rave($_ENV['SECRET_KEY']);
    }

    function getBalances()
    {
        $this->misc->setEndPoint("v3/balances");//set the endpoint for the api call


        return $this->misc->getTransferBalance($array);
    }

    function getBalance($array)
    {

        if (!isset($array['currency'])) {
            $array['currency'] = 'NGN';
        }


        //set the payment handler
        $this->misc->setEndPoint("v3/balances/" . $array['currency']);


        return $this->misc->getTransferBalance($array);

    }

    function verifyAccount($array)
    {

        //set the payment handler
        $this->misc->setEndPoint("v3/accounts/resolve");


        return $this->misc->verifyAccount($array);

    }
}
