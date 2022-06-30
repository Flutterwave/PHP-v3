<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php


use Flutterwave\EventHandlers\SubscriptionEventHandler;

class Subscription
{
    protected $subscription;

    function __construct()
    {
        $this->subscription = new Rave($_ENV['SECRET_KEY']);
    }

    function activateSubscription($id)
    {
        //set the payment handler
        $endPoint = 'v3/subscriptions/' . $id . '/activate';
        $this->subscription->eventHandler(new SubscriptionEventHandler)
            //set the endpoint for the api call
            ->setEndPoint($endPoint);
        //returns the value from the results
        SubscriptionEventHandler::startRecording();
        $response = $this->subscription->activateSubscription();
        SubscriptionEventHandler::sendAnalytics('Activate-Subscriptions');

        return $response;
    }

    function getAllSubscription()
    {
        //set the payment handler
        $this->subscription->eventHandler(new SubscriptionEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subscriptions");
        //returns the value from the results
        SubscriptionEventHandler::startRecording();
        $response = $this->subscription->getAllSubscription();
        SubscriptionEventHandler::sendAnalytics('Get-All-Subscriptions');

        return $response;
    }

    function cancelSubscription($id)
    {
        $endPoint = 'v3/subscriptions/' . $id . '/cancel';
        //set the payment handler

        $this->subscription->eventHandler(new SubscriptionEventHandler)
            //set the endpoint for the api call
            ->setEndPoint($endPoint);
        //returns the value from the results
        SubscriptionEventHandler::startRecording();
        $response= $this->subscription->cancelSubscription();
        SubscriptionEventHandler::sendAnalytics('Cancel-Subscription');

        return $response;
    }
}

