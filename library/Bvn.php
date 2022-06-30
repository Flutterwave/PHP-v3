<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

use Flutterwave\EventHandlers\BvnEventHandler;

class Bvn
{
    protected $bvn;

    function __construct()
    {
        $this->bvn = new Rave($_ENV['SECRET_KEY']);
    }

    function verifyBVN($bvn)
    {
        //set the payment handler
        $this->bvn->eventHandler(new BvnEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/kyc/bvns");
        //returns the value from the results

        BvnEventHandler::startRecording();
        $response= $this->bvn->bvn($bvn);
        BvnEventHandler::sendAnalytics("Verify-BVN");

        return $response;
    }
}
