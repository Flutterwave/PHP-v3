<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\SubaccountEventHandler;

class Subaccount
{
    protected $subaccount;

    function __construct()
    {
        $this->subaccount = new Rave($_ENV['SECRET_KEY']);
    }

    function createSubaccount($array)
    {
        //set the payment handler
        $this->subaccount->eventHandler(new SubaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts");
        //returns the value from the results
        SubaccountEventHandler::startRecording();
        $response = $this->subaccount->createSubaccount($array);
        SubaccountEventHandler::sendAnalytics('Create-Subaccount');

        return $response;
    }

    function getSubaccounts()
    {

        $this->subaccount->eventHandler(new SubaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts");
        //returns the value from the results
        SubaccountEventHandler::startRecording();
        $response = $this->subaccount->getSubaccounts();
        SubaccountEventHandler::sendAnalytics('Get-Subaccounts');

        return $response;
    }

    function fetchSubaccount($array)
    {

        $this->subaccount->eventHandler(new SubaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results
        SubaccountEventHandler::startRecording();
        $response = $this->subaccount->fetchSubaccount();
        SubaccountEventHandler::sendAnalytics('Fetch-Subaccount');

        return $response;

    }

    function updateSubaccount($array)
    {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing <b> id </b> Parameter in the payload
              </div>';
        }

        $this->subaccount->eventHandler(new SubaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results
        SubaccountEventHandler::startRecording();
        $response = $this->subaccount->updateSubaccount($array);
        SubaccountEventHandler::sendAnalytics('Update-Subaccount');

        return $response;

    }

    function deleteSubaccount($array)
    {
        $this->subaccount->eventHandler(new SubaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results

        SubaccountEventHandler::startRecording();
        $response = $this->subaccount->deleteSubaccount();
        SubaccountEventHandler::sendAnalytics('Delete-Subaccount');

        return $response;
    }
}
