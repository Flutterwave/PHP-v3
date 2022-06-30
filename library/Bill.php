<?php

namespace Flutterwave;


use Flutterwave\EventHandlers\BillEventHandler;

class Bill
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = array('AIRTIME', 'DSTV', 'DSTV BOX OFFICE', 'Postpaid', 'Prepaid', 'AIRTEL', 'IKEDC TOP UP', 'EKEDC POSTPAID TOPUP', 'EKEDC PREPAID TOPUP', 'LCC', 'KADUNA TOP UP');
    }

    function payBill($array)
    {
        if (gettype($array['amount']) !== 'integer') {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Specified Amount should be an integer and not a string.
          </div>';
        }

        if (!in_array($array['type'], $this->type, true)) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The Type specified in the payload  is not <b> "' . $this->type[0] . ' , ' . $this->type[1] . ' , ' . $this->type[2] . ' or ' . $this->type[3] . '"</b>
          </div>';
        }
        switch ($array['type']) {
            case 'DSTV':
                //set type to dstv

                $this->type = 'DSTV';

                break;

            case 'EKEDC POSTPAID TOPUP':
                //set type to ekedc

                $this->type = 'EKEDC POSTPAID TOPUP';

                break;
            case 'LCC':
                //set type to lcc

                $this->type = 'LCC';

                break;
            case 'AIRTEL':
                //set type to airtel

                $this->type = 'AIRTEL';

                break;
            case 'Postpaid':
                //set type to postpaid

                $this->type = 'Postpaid';

                break;
            case 'IKEDC TOP UP':
                //set type to ikedc

                $this->type = 'IKEDC TOP UP';

                break;
            case 'KADUNA TOP UP':
                //set type to kaduna top up

                $this->type = 'KADUNA TOP UP';

                break;

            case 'DSTV BOX OFFICE':
                //set type to dstv box office
                $this->type = 'DSTV BOX OFFICE';

                break;

            default:
                //set type to airtime
                $this->type = 'AIRTIME';

                break;
        }

        $this->payment->eventHandler(new BillEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bills");

        BillEventHandler::startRecording();
        $response = $this->payment->bill($array);
        BillEventHandler::sendAnalytics("Pay-Bills");

        return $response;
    }

    function bulkBill($array)
    {
        if (!array_key_exists('bulk_reference', $array) || !array_key_exists('callback_url', $array) || !array_key_exists('bulk_data', $array)) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Please Enter the required body parameters for the request.
          </div>';
        }

        $this->payment->eventHandler(new BillEventHandler)
            ->setEndPoint('v3/bulk-bills');

        BillEventHandler::startRecording();
        $response = $this->payment->bulkBills($array);
        BillEventHandler::sendAnalytics("Pay-Bulk-Bills");

        return $response;
    }

    function getBill($array)
    {

        $this->payment->eventHandler(new BillEventHandler);

        if (array_key_exists('reference', $array) && !array_key_exists('from', $array)) {
            echo "Im here";
            $this->payment->setEndPoint('v3/bills/' . $array['reference']);
        } else if (array_key_exists('code', $array) && !array_key_exists('customer', $array)) {
            $this->payment->setEndPoint('v3/bill-items');
        } else if (array_key_exists('id', $array) && array_key_exists('product_id', $array)) {
            $this->payment->setEndPoint('v3/billers');
        } else if (array_key_exists('from', $array) && array_key_exists('to', $array)) {
            if (isset($array['page']) && isset($array['reference'])) {
                $this->payment->setEndPoint('v3/bills');
            } else {
                $this->payment->setEndPoint('v3/bills');
            }
        }

        BillEventHandler::startRecording();
        $response = $this->payment->getBill($array);
        BillEventHandler::sendAnalytics("Get-Bills");

        return $response;
    }

    function getBillCategories()
    {


        $this->payment->eventHandler(new BillEventHandler)
            ->setEndPoint('v3');

        BillEventHandler::startRecording();
        $response = $this->payment->getBillCategories();
        BillEventHandler::sendAnalytics("Get-Bill-Categories");

        return $response;
    }

    function getAgencies()
    {
        $this->payment->eventHandler(new BillEventHandler)
            ->setEndPoint('v3');

        BillEventHandler::startRecording();
        $response = $this->payment->getBillers();
        BillEventHandler::sendAnalytics("Get-Billing-Agencies");

        return $response;
    }
}








