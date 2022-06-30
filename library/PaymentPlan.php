<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php


use Flutterwave\EventHandlers\PaymentPlanEventHandler;

class PaymentPlan
{
    protected $plan;

    function __construct() {
        $this->plan = new Rave($_ENV['SECRET_KEY']);
    }

    function createPlan($array) {
        //set the payment handler
        $this->plan->eventHandler(new PaymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans");

        if (empty($array['amount']) || !array_key_exists('amount', $array) ||
            empty($array['name']) || !array_key_exists('name', $array) ||
            empty($array['interval']) || !array_key_exists('interval', $array) ||
            empty($array['duration']) || !array_key_exists('duration', $array)) {

            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for the following parameters  <b> amount, name , interval, or duration </b>
              </div>';
        }

        // if(!empty($array['amount'])){

        // }

        //returns the value from the results
        PaymentPlanEventHandler::startRecording();
        $response = $this->plan->createPlan($array);
        PaymentPlanEventHandler::sendAnalytics('Initiate-Create-Plan');

        return $response;

    }

    function updatePlan($array) {

        if (!isset($array['id']) || !isset($array['name']) || !isset($array['status'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for a parametter: <b> id, name, or status</b>
              </div>';
        }

        //set the payment handler
        $this->plan->eventHandler(new PaymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/" . $array['id']);

        PaymentPlanEventHandler::startRecording();
        $response = $this->plan->updatePlan($array);
        PaymentPlanEventHandler::sendAnalytics('Initiate-Update-Plan');

        return $response;

    }

    function cancelPlan($array) {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing values for a parametter: <b> id</b>
              </div>';
        }

        //set the payment handler
        $this->plan->eventHandler(new PaymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/" . $array['id'] . "/cancel");

        PaymentPlanEventHandler::startRecording();
        $response = $this->plan->cancelPlan($array);
        PaymentPlanEventHandler::sendAnalytics('Initiate-Cancel-Plan');

        return $response;
    }

    function getPlans() {
        //set the payment handler
        $this->plan->eventHandler(new PaymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans");

        PaymentPlanEventHandler::startRecording();
        $response = $this->plan->getPlans();
        PaymentPlanEventHandler::sendAnalytics('Get-Plans');

        return $response;
    }

    function get_a_Plan($array) {
        //set the payment handler
        $this->plan->eventHandler(new PaymentPlanEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/payment-plans/" . $array['id']);

        PaymentPlanEventHandler::startRecording();
        $response = $this->plan->get_a_Plan();
        PaymentPlanEventHandler::sendAnalytics('Get-A-Plan');

        return $response;
    }
}

