<?php

namespace Flutterwave\EventHandlers;

class BankTransferEventHandler implements EventHandlerInterface
{
    use EventTracker;

    /**
     * @inheritDoc
     */
    function onSuccessful($transactionData)
    {
        // TODO: Implement onSuccessful() method.
    }

    /**
     * @inheritDoc
     */
    function onFailure($transactionData)
    {
        // TODO: Implement onFailure() method.
    }

    /**
     * @inheritDoc
     */
    function onRequery($transactionReference)
    {
        // TODO: Implement onRequery() method.
    }

    /**
     * @inheritDoc
     */
    function onRequeryError($requeryResponse)
    {
        // TODO: Implement onRequeryError() method.
    }

    /**
     * @inheritDoc
     */
    function onCancel($transactionReference)
    {
        // TODO: Implement onCancel() method.
    }

    /**
     * @inheritDoc
     */
    function onTimeout($transactionReference, $data)
    {
        // TODO: Implement onTimeout() method.
    }

    /**
     * @throws \Exception
     * */
    function onAuthorization(\stdClass $response, ?array $resource = null): array
    {
        $auth = $response->meta->authorization;
        $mode = $auth->mode;
        $data['dev_instruction'] = "Display the transfer data for the user to make a transfer to the generated account number. verify via Webhook Service.";
        $data['instruction'] = $auth->transfer_note;
        $data['transfer_reference'] = $auth->transfer_reference;
        $data['transfer_account'] = $auth->transfer_account;
        $data['transfer_bank'] = $auth->transfer_bank;
        $data['account_expiration'] = $auth->account_expiration;
        $data['transfer_amount'] = $auth->transfer_amount;
        $data['mode'] = $mode;

        if(is_array($resource) && !empty($resource))
        {
            $logger = $resource['logger'];
            $logger->notice("Transfer Authorization Mode: ".$mode);
            $logger->info("Bank Transfer Event::Created Account Info :".json_encode($data));
        }

        return $data;
    }
}