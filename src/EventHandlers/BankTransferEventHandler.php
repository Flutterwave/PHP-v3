<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

use Flutterwave\Contract\ConfigInterface;

class BankTransferEventHandler implements EventHandlerInterface
{
    use EventTracker;

    private static ConfigInterface $config;
    public function __construct($config)
    {
        self::$config = $config;
    }

    /**
     * @inheritDoc
     */
    public function onSuccessful($transactionData): void
    {
        // TODO: Implement onSuccessful() method.
    }

    /**
     * @inheritDoc
     */
    public function onFailure($transactionData): void
    {
        // TODO: Implement onFailure() method.
    }

    /**
     * @inheritDoc
     */
    public function onRequery($transactionReference): void
    {
        // TODO: Implement onRequery() method.
    }

    /**
     * @inheritDoc
     */
    public function onRequeryError($requeryResponse): void
    {
        // TODO: Implement onRequeryError() method.
    }

    /**
     * @inheritDoc
     */
    public function onCancel($transactionReference): void
    {
        // TODO: Implement onCancel() method.
    }

    /**
     * @inheritDoc
     */
    public function onTimeout($transactionReference, $data): void
    {
        // TODO: Implement onTimeout() method.
    }

    /**
     * @param  \stdClass  $response
     * @param  array|null $resource
     * @return array
     */
    public function onAuthorization(\stdClass $response, ?array $resource = null): array
    {
        $auth = $response->meta->authorization;
        $mode = $auth->mode;
        $data['dev_instruction'] = 'Display the transfer data for the user to make a transfer to the generated account number. verify via Webhook Service.';
        $data['instruction'] = $auth->transfer_note;
        $data['transfer_reference'] = $auth->transfer_reference;
        $data['transfer_account'] = $auth->transfer_account;
        $data['transfer_bank'] = $auth->transfer_bank;
        $data['account_expiration'] = $auth->account_expiration;
        $data['transfer_amount'] = $auth->transfer_amount;
        $data['mode'] = $mode;

        if (is_array($resource) && ! empty($resource)) {
            $logger = $resource['logger'];
            $logger->notice('Transfer Authorization Mode: ' . $mode);
            $logger->info('Bank Transfer Event::Created Account Info :' . json_encode($data));
        }

        return $data;
    }
}
