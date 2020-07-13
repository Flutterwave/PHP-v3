<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');

use Flutterwave\Rave;

class VirtualCard {
    protected $vc;
    //initialise the constructor
    function __construct(){
        $this->vc = new Rave($_ENV['SECRET_KEY']);
    }
    //create card function
    function createCard($array){
            //set the endpoint for the api call
            if(!isset($array['currency']) || !isset($array['amount']) || !isset($array['billing_name'])){
                return '<div class="alert alert-danger" role="alert">
            Please pass the required values for <b> currency, duration and amount</b>
          </div>';
            }else{
                $this->vc->setEndPoint("v3/virtual-cards");

            return $this->vc->vcPostRequest($array);
            }

            
        }
    //get the detials of a card using the card id
    function getCard($array){

        if(!isset($array['id'])){
            return '<div class="alert alert-danger" role="alert">
        Please pass the required value for <b> id</b>
      </div>';
        }else{
            //set the endpoint for the api call
            $this->vc->setEndPoint("v3/virtual-cards/".$array['id']);
            return $this->vc->vcGetRequest();
        }
            
        }
    //list all the virtual cards on your profile
    function listCards(){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v3/virtual-cards");
            return $this->vc->vcGetRequest();
            
        }
    //terminate a virtual card on your profile
    function terminateCard($array){

        if(!isset($array['id'])){
            return '<div class="alert alert-danger" role="alert">
        Please pass the required value for <b> id </b>
      </div>';
        }else{
            //set the endpoint for the api call
            $this->vc->setEndPoint("v3/virtual-cards/".$array['id']."/terminate");
            return $this->vc->vcPutRequest();
        }
     
    }
    //fund a virtual card
    function fundCard($array){
            //set the endpoint for the api call
            if(gettype($array['amount']) !== 'integer'){
                $array['amount'] = (int) $array['amount'];
            }
            if(!isset($array['currency'])){
                $array['currency'] = 'NGN';
            }
            $this->vc->setEndPoint("v3/virtual-cards/".$array['id']."/fund");

            $data = array(
                "amount"=> $array['amount'],
                "debit_currency"=> $array['currency']
            );
            return $this->vc->vcPostRequest($data);
        }
   // list card transactions
    function cardTransactions($array){
            //set the endpoint for the api call
            $this->vc->setEndPoint("v3/virtual-cards/".$array['id']."/transactions");
            return $this->vc->vcGetRequest($array);
        }
    //withdraw funds from card
    function cardWithdrawal($array){
            //set the endpoint for the api call
            if(!isset($array['amount'])){
                return '<div class="alert alert-danger" role="alert">
                Please pass the required value for <b> amount</b>
              </div>';
            }

            $this->vc->setEndPoint("v3/virtual-cards/".$array['id']."/withdraw");
            return $this->vc->vcPostRequest($array);
        }

    function block_unblock_card($array){
        if(!isset($array['id']) || !isset($array['status_action'])){
            return '<div class="alert alert-danger" role="alert">
            Please pass the required value for <b> id and status_action </b>
          </div>';
        }
        $this->vc->setEndPoint("v3/virtual-cards/".$array['id']."/"."status/".$array['status_action']);
        return $this->vc->vcPutRequest();

    }
        
    }
?>