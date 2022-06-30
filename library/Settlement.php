<?php

namespace Flutterwave;


use Flutterwave\EventHandlers\SettlementEventHandler;

class Settlement
{
    function __construct() {
        $this->settle = new Rave($_ENV['PUBLIC_KEY'], $_ENV['SECRET_KEY'], $_ENV['ENV']);
    }

    function fetchSettlement($array) {
        //set the payment handler
        $this->subscription->eventHandler(new SettlementEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/settlements/" . $array['id']);
        //returns the value from the results

        SettlementEventHandler::startRecording();
        $response = $this->settle->fetchASettlement();
        SettlementEventHandler::sendAnalytics('Fetch-Settlement');

        return $response;

    }

    function listAllSettlements() {
        //set the payment handler
        $this->settle->eventHandler(new SettlementEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/settlements");
        //returns the value from the results

        SettlementEventHandler::startRecording();
        $response = $this->settle->getAllSettlements();
        SettlementEventHandler::sendAnalytics('List-All-Settlements');

        return $response;

    }

}
