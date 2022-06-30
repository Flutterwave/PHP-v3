<?php

namespace Flutterwave;


use Flutterwave\EventHandlers\UssdEventHandler;
use Flutterwave\EventHandlers\VirtualAccountEventHandler;

class VirtualAccount
{

    function __construct() {
        $this->va = new Rave($_ENV['SECRET_KEY']);
    }

    /**
     * Creating the VirtualAccount
     */

    function createvirtualAccount($userdata) {

        if (!isset($userdata['email']) || !isset($userdata['duration']) || !isset($userdata['frequency'])
            || !isset($userdata['amount'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The following body params are required:  <b> email, duration, frequency, or amount </b>
          </div>';
        }


        $this->va->eventHandler(new VirtualAccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/virtual-account-numbers");

        //returns the value of the result.
        UssdEventHandler::startRecording();
        $response = $this->va->createVirtualAccount($userdata);
        UssdEventHandler::sendAnalytics('Create-Virtual-Account');

        return $response;


    }

    function createBulkAccounts($array) {

        $this->va->eventHandler(new VirtualAccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-virtual-account-numbers");

        //returns the value of the result.
        UssdEventHandler::startRecording();
        $response = $this->va->createBulkAccounts($array);
        UssdEventHandler::sendAnalytics('Create-Bulk-Virtual-Account');

        return $response;
    }

    function getBulkAccounts($array) {
        if (!isset($array['batch_id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
        The following body params are required:  <b> batch_id </b>
      </div>';
        }

        $this->va->eventHandler(new VirtualAccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-virtual-account-numbers/" . $array['batch_id']);

        //returns the value of the result.
        UssdEventHandler::startRecording();
        $response = $this->va->getBulkAccounts($array);
        UssdEventHandler::sendAnalytics('Get-Bulk-Virtual-Account');

        return $response;

    }

    function getAccountNumber($array) {

        if (!isset($array['order_ref'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The following body params are required:  <b> order_ref </b>
          </div>';
        }

        $this->va->eventHandler(new VirtualAccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/virtual-account-numbers/" . $array['order_ref']);

        //returns the value of the result.
        UssdEventHandler::startRecording();
        $response = $this->va->getvAccountsNum();
        UssdEventHandler::sendAnalytics('Get-Virtual-Account-number');

        return $response;
    }


}




