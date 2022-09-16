<?php

namespace Flutterwave\EventHandlers;

class ApplePayEventHandler implements EventHandlerInterface
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

    function onAuthorization(\stdClass $response, ?array $resource = null): array
    {
        if(property_exists($response, 'data')){
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $data['data_to_save'] = [
                "transactionId" => $transactionId,
                "tx_ref" => $tx_ref
            ];
            $data['mode'] = $response->data->meta->authorization->mode;
        }

        $data['dev_instruction'] = "Redirect the user to the auth link for validation. verfiy via the verify endpoint.";
        $data['url'] = $response->data->meta->authorization->redirect;

        if(is_array($resource) && !empty($resource))
        {
            $logger = $resource['logger'];
            $logger->notice("Apple Method Event::Apple Authorization Mode: ".$data['mode']?? "redirect");
        }

        return $data;
    }
}