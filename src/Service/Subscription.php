<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Unirest\Exception;

class Subscription extends Service
{
    use EventTracker;
    private string $name = 'subscriptions';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->logger->notice('Subscription Service::Retrieving all Subscriptions.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function activate(string $id): \stdClass
    {
        $this->logger->notice("Subscription Service::Activating a Subscriptions [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name."/{$id}/activate");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function deactivate(string $id): \stdClass
    {
        $this->logger->notice("Subscription Service::Deactivating a Subscriptions [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name."/{$id}/cancel");
        self::setResponseTime();
        return $response;
    }
}
