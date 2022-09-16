<?php

namespace Flutterwave\EventHandlers;

class PayoutSubaccoutEventHandler implements EventHandlerInterface
{
    use EventTracker;

    function onSuccessful($transactionData)
    {
        // TODO: Implement onSuccessful() method.
    }

    function onFailure($transactionData)
    {
        // TODO: Implement onFailure() method.
    }

    function onRequery($transactionReference)
    {
        // TODO: Implement onRequery() method.
    }

    function onRequeryError($requeryResponse)
    {
        // TODO: Implement onRequeryError() method.
    }

    function onCancel($transactionReference)
    {
        // TODO: Implement onCancel() method.
    }

    function onTimeout($transactionReference, $data)
    {
        // TODO: Implement onTimeout() method.
    }
}