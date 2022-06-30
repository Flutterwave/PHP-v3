<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\TransferEventHandler;

class Transfer
{
    protected $transfer;

    function __construct()
    {
        $this->transfer = new Rave($_ENV['SECRET_KEY']);
    }

    //initiating a single transfer
    function singleTransfer($array)
    {
        //set the payment handler
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers");
        //returns the value from the results
        TransferEventHandler::startRecording();
        $response = $this->transfer->transferSingle($array);
        TransferEventHandler::sendAnalytics('Initiate-Single-Transfer');

        return $response;
    }

    //initiating a bulk transfer
    function bulkTransfer($array)
    {
        //set the payment handler
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-transfers");
        //returns the value from the results
        TransferEventHandler::startRecording();
        $response = $this->transfer->transferBulk($array);
        TransferEventHandler::sendAnalytics('Initiate-Bulk-Transfer');

        return $response;
    }

    function listTransfers($array = array('url' => 'blank'))
    {
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers");

        TransferEventHandler::startRecording();
        $response = $this->transfer->listTransfers($array);
        TransferEventHandler::sendAnalytics('List-Transfer');

        return $response;


        //set the payment handler

    }

    function bulkTransferStatus($array)
    {

        //set the payment handler
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-transfers");

        TransferEventHandler::startRecording();
        $response = $this->transfer->bulkTransferStatus($array);
        TransferEventHandler::sendAnalytics('Bulk-Transfer-Status');

        return $response;
    }

    function getTransferFee($array)
    {

        if (in_array('amount', $array) && gettype($array['amount']) !== "string") {
            $array['amount'] = (string)$array['amount'];
        }

        //set the payment handler
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers/fee");

        TransferEventHandler::startRecording();
        $response = $this->transfer->applicableFees($array);
        TransferEventHandler::sendAnalytics('Get-Transfer-Fee');

        return $response;
    }


    function getBanksForTransfer($data = array("country" => 'NG'))
    {

        //set the payment handler
        $this->transfer->eventHandler(new TransferEventHandler)
            //set the endpoint for the api call

            ->setEndPoint("v2/banks/" . $data['country'] . "/");

        TransferEventHandler::startRecording();
        $response= $this->transfer->getBanksForTransfer();
        TransferEventHandler::sendAnalytics('Get-Banks-For-Transfer');

        return $response;
    }


    function verifyTransaction()
    {
        //verify the charge
        return $this->transfer->verifyTransaction($this->transfer->txref);//Uncomment this line if you need it
    }


}

