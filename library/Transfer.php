<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');
require_once('raveEventHandlerInterface.php');
require_once('EventTracker.php');

class transferEventHandler implements EventHandlerInterface
{
    use EventTracker;

    /**
     * This is called only when a transaction is successful
     * @param array
     * */
    function onSuccessful($transactionData)
    {
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
        self::sendAnalytics("Initiate-Transfer");
    }

    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData)
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
        self::sendAnalytics("Initiate-Transfer-error");

    }

    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference)
    {
        // Do something, anything!
    }

    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse)
    {
        // Do something, anything!
    }

    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference)
    {
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution

    }

    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data)
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects

    }
}

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
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers");
        //returns the value from the results
        transferEventHandler::startRecording();
        $response = $this->transfer->transferSingle($array);
        transferEventHandler::sendAnalytics('Initiate-Single-Transfer');

        return $response;
    }

    //initiating a bulk transfer
    function bulkTransfer($array)
    {
        //set the payment handler
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-transfers");
        //returns the value from the results
        transferEventHandler::startRecording();
        $response = $this->transfer->transferBulk($array);
        transferEventHandler::sendAnalytics('Initiate-Bulk-Transfer');

        return $response;
    }

    function listTransfers($array = array('url' => 'blank'))
    {
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers");

        transferEventHandler::startRecording();
        $response = $this->transfer->listTransfers($array);
        transferEventHandler::sendAnalytics('List-Transfer');

        return $response;


        //set the payment handler

    }

    function bulkTransferStatus($array)
    {

        //set the payment handler
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bulk-transfers");

        transferEventHandler::startRecording();
        $response = $this->transfer->bulkTransferStatus($array);
        transferEventHandler::sendAnalytics('Bulk-Transfer-Status');

        return $response;
    }

    function getTransferFee($array)
    {

        if (in_array('amount', $array) && gettype($array['amount']) !== "string") {
            $array['amount'] = (string)$array['amount'];
        }

        //set the payment handler
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/transfers/fee");

        transferEventHandler::startRecording();
        $response = $this->transfer->applicableFees($array);
        transferEventHandler::sendAnalytics('Get-Transfer-Fee');

        return $response;
    }


    function getBanksForTransfer($data = array("country" => 'NG'))
    {

        //set the payment handler
        $this->transfer->eventHandler(new transferEventHandler)
            //set the endpoint for the api call

            ->setEndPoint("v2/banks/" . $data['country'] . "/");

        transferEventHandler::startRecording();
        $response= $this->transfer->getBanksForTransfer();
        transferEventHandler::sendAnalytics('Get-Banks-For-Transfer');

        return $response;
    }


    function verifyTransaction()
    {
        //verify the charge
        return $this->transfer->verifyTransaction($this->transfer->txref);//Uncomment this line if you need it
    }


}

?>
