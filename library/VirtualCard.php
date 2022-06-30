<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\EventTracker;

class VirtualCard
{
    use EventTracker;

    protected $vc;

    //initialise the constructor
    function __construct()
    {
        $this->vc = new Rave($_ENV['SECRET_KEY']);
    }

    //create card function
    function createCard($array)
    {
        //set the endpoint for the api call
        if (!isset($array['currency']) || !isset($array['amount']) || !isset($array['billing_name'])) {
            return '<div class="alert alert-danger" role="alert">
            Please pass the required values for <b> currency, duration and amount</b>
          </div>';
        } else {
            $this->vc->setEndPoint("v3/virtual-cards");

            self::startRecording();
            $response = $this->vc->vcPostRequest($array);
            self::sendAnalytics('Create-Virtual-Card');

            return $response;
        }


    }

    //get the detials of a card using the card id
    function getCard($array)
    {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert">
        Please pass the required value for <b> id</b>
      </div>';
        } else {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v3/virtual-cards/" . $array['id']);

            self::startRecording();
            $response = $this->vc->vcGetRequest();
            self::sendAnalytics('Get-Virtual-Card');

            return $response;
        }

    }

    //list all the virtual cards on your profile
    function listCards()
    {
        //set the endpoint for the api call
        $this->vc->setEndPoint("v3/virtual-cards");

        self::startRecording();
        $response = $this->vc->vcGetRequest();
        self::sendAnalytics('List-Cards');

        return $response;

    }

    //terminate a virtual card on your profile
    function terminateCard($array)
    {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert">
        Please pass the required value for <b> id </b>
      </div>';
        } else {
            //set the endpoint for the api call

            $this->vc->setEndPoint("v3/virtual-cards/" . $array['id'] . "/terminate");

            self::startRecording();
            $response = $this->vc->vcPutRequest();
            self::sendAnalytics('Terminate-Card');

            return $response;
        }

    }

    //fund a virtual card
    function fundCard($array)
    {
        //set the endpoint for the api call
        if (gettype($array['amount']) !== 'integer') {
            $array['amount'] = (int)$array['amount'];
        }
        if (!isset($array['currency'])) {
            $array['currency'] = 'NGN';
        }
        $this->vc->setEndPoint("v3/virtual-cards/" . $array['id'] . "/fund");

        $data = array(
            "amount" => $array['amount'],
            "debit_currency" => $array['currency']
        );

        self::startRecording();
        $response = $this->vc->vcPostRequest($data);
        self::sendAnalytics('Terminate-Card');

        return $response;
    }

    // list card transactions
    function cardTransactions($array)
    {
        //set the endpoint for the api call
        $this->vc->setEndPoint("v3/virtual-cards/" . $array['id'] . "/transactions");

        self::startRecording();
        $response = $this->vc->vcGetRequest($array);
        self::sendAnalytics('List-Transactions');

        return $response;
    }

    //withdraw funds from card
    function cardWithdrawal($array)
    {
        //set the endpoint for the api call
        if (!isset($array['amount'])) {
            return '<div class="alert alert-danger" role="alert">
                Please pass the required value for <b> amount</b>
              </div>';
        }

        $this->vc->setEndPoint("v3/virtual-cards/" . $array['id'] . "/withdraw");

        self::startRecording();
        $response = $this->vc->vcPostRequest($array);
        self::sendAnalytics('Initiate-Card-Withdrawal');

        return $response;
    }

    function block_unblock_card($array)
    {
        if (!isset($array['id']) || !isset($array['status_action'])) {
            return '<div class="alert alert-danger" role="alert">
            Please pass the required value for <b> id and status_action </b>
          </div>';
        }
        $this->vc->setEndPoint("v3/virtual-cards/" . $array['id'] . "/" . "status/" . $array['status_action']);

        self::startRecording();
        $response = $this->vc->vcPutRequest();
        self::sendAnalytics('Initiate-Card-Withdrawal');

        return $response;

    }

}

