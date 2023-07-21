<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

use Flutterwave\Contract\ConfigInterface;

class GooglePayEventHandler implements EventHandlerInterface
{
    use EventTracker;

    private static ConfigInterface $config;
    public function __construct($config)
    {
        self::$config = $config;
    }

    public function onSuccessful($transactionData): void
    {
        // TODO: Implement onSuccessful() method.
    }

    public function onFailure($transactionData): void
    {
        // TODO: Implement onFailure() method.
    }

    public function onRequery($transactionReference): void
    {
        // TODO: Implement onRequery() method.
    }

    public function onRequeryError($requeryResponse): void
    {
        // TODO: Implement onRequeryError() method.
    }

    public function onCancel($transactionReference): void
    {
        // TODO: Implement onCancel() method.
    }

    public function onTimeout($transactionReference, $data): void
    {
        // TODO: Implement onTimeout() method.
    }

    public function onAuthorization(\stdClass $response, ?array $resource = null): array
    {
        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $data['data_to_save'] = [
                'transactionId' => $transactionId,
                'tx_ref' => $tx_ref,
            ];
            $data['mode'] = $response->data->meta->authorization->mode;
        }

        $data['dev_instruction'] = 'Redirect the user to the auth link for validation. verfiy via the verify endpoint.';
        $data['url'] = $response->data->meta->authorization->redirect;

        if (is_array($resource) && ! empty($resource)) {
            $logger = $resource['logger'];
            $logger->notice('Google Method Event::Apple Authorization Mode: ' . $data['mode'] ?? 'redirect');
        }

        return $data;
    }
}