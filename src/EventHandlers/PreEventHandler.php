<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

use Flutterwave\Contract\ConfigInterface;

class PreEventHandler implements EventHandlerInterface
{
    use EventTracker;

    private static ConfigInterface $config;
    public function __construct($config)
    {
        self::$config = $config;
    }

    public function onSuccessful($transactionData): void
    {
        self::sendAnalytics('Initiate-Preauth');
    }

    public function onFailure($transactionData): void
    {
        self::sendAnalytics('Initiate-Preauth-Error');
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
}
