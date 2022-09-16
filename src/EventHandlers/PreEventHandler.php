<?php

namespace Flutterwave\EventHandlers;

class PreEventHandler implements EventHandlerInterface
{

    use EventTracker;

    function onSuccessful($transactionData) {
        self::sendAnalytics("Initiate-Preauth");
    }

    function onFailure($transactionData) {
        self::sendAnalytics("Initiate-Preauth-Error");
    }

    function onRequery($transactionReference) {
        // TODO: Implement onRequery() method.
    }

    function onRequeryError($requeryResponse) {
        // TODO: Implement onRequeryError() method.
    }

    function onCancel($transactionReference) {
        // TODO: Implement onCancel() method.
    }

    function onTimeout($transactionReference, $data) {
        // TODO: Implement onTimeout() method.
    }
}