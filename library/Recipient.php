<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php


use Flutterwave\EventHandlers\RecipientEventHandler;

class Recipient
{
    protected $recipient;

    function __construct()
    {
        $this->recipient = new Rave($_ENV['SECRET_KEY']);
    }

    function createRecipient($array)
    {
        //set the payment handler

        if (!isset($array['account_number']) || !isset($array['account_bank'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following body params are required <b> account_number and account_bank </b>
              </div>';
        }
        $this->recipient->eventHandler(new RecipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries");
        //returns the value from the results

        RecipientEventHandler::startRecording();
        $response = $this->recipient->createBeneficiary($array);
        RecipientEventHandler::sendAnalytics('Create-Recipient');

        return $response;
    }

    function listRecipients()
    {
        $this->recipient->eventHandler(new RecipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries");
        //returns the value from the results
        RecipientEventHandler::startRecording();
        $response = $this->recipient->getBeneficiaries();
        RecipientEventHandler::sendAnalytics('List-Recipients');

        return $response;
    }

    function fetchBeneficiary($array)
    {
        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following PATH param is required :<b> id </b>
              </div>';
        }

        if (gettype($array['id']) !== 'string') {
            $array['id'] = (string)$array['id'];
        }

        $this->recipient->eventHandler(new RecipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries/" . $array['id']);
        //returns the value from the results
        RecipientEventHandler::startRecording();
        $response = $this->recipient->getBeneficiaries();
        RecipientEventHandler::sendAnalytics('Fetch-Beneficiary');

        return $response;
    }

    function deleteBeneficiary($array)
    {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                The following PATH param is required :<b> id </b>
              </div>';
        }

        if (gettype($array['id']) !== 'string') {
            $array['id'] = (string)$array['id'];
        }

        $this->recipient->eventHandler(new RecipientEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/beneficiaries/" . $array['id']);
        //returns the value from the results

        RecipientEventHandler::startRecording();
        $response= $this->recipient->deleteBeneficiary();
        RecipientEventHandler::sendAnalytics('Delete-Beneficiary');

        return $response;
    }

}

?>
