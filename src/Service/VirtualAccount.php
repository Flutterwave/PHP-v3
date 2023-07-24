<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;

class VirtualAccount extends Service
{
    use EventTracker;

    private string $name = 'virtual-account-numbers';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function create(array $payload): \stdClass
    {
        $this->logger->notice('VirtualAccount Service::Creating new Virtual Account.');

        //check email and bvn are in payload
        if (! isset($payload['email']) || ! isset($payload['bvn'])) {
            $msg = 'The required parameter email or bvn is not present in payload';
            $this->logger->error('VirtualAccount Service::' . $msg);
            throw new \InvalidArgumentException($msg);
        }

        $this->logger->notice('VirtualAccount Service::Payload Confirmed.');
        self::startRecording();
        $response = $this->request($payload, 'POST', "{$this->name}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function createBulk(array $payload): \stdClass
    {
        if (! isset($payload['is_permanent'])) {
            $payload['is_permanent'] = false;
        }

        $this->logger->notice('VirtualAccount Service::Creating Bulk Virtual Accounts.');
        //check accounts and email are in payload
        if (! isset($payload['accounts']) || ! isset($payload['email'])) {
            $msg = 'The required parameter accounts or email is not present in payload';
            $this->logger->error('VirtualAccount Service::' . $msg);
            throw new \InvalidArgumentException($msg);
        }

        $this->logger->notice('VirtualAccount Service:: Payload Confirmed [Bulk].');

        self::startRecording();
        $response = $this->request($payload, 'POST', 'bulk-virtual-account-numbers');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function get($order_ref): \stdClass
    {
        $this->logger->notice("VirtualAccount Service::Retrieving Virtual Account [{$order_ref}].");
        self::startRecording();
        $response = $this->request(null, 'GET', "{$this->name}/{$order_ref}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getBulk($batch_id): \stdClass
    {
        $this->logger->notice("VirtualAccount Service::Retrieving Bulk Virtual Accounts [{$batch_id}].");
        self::startRecording();
        $response = $this->request(null, 'GET', "bulk-virtual-account-numbers/{$batch_id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function update(array $payload): \stdClass
    {
        //check email and bvn are in payload
        if (! isset($payload['order_ref']) || ! isset($payload['bvn'])) {
            $msg = 'The required parameter order_ref or bvn is not present in payload';
            $this->logger->error('VirtualAccount Service::' . $msg);
            throw new \InvalidArgumentException($msg);
        }

        $order_ref = $payload['order_ref'];

        $this->logger->notice("VirtualAccount Service::Updating Virtual Account. [{$order_ref}]");

        $this->logger->notice('VirtualAccount Service::Payload Confirmed.');
        self::startRecording();
        $response = $this->request($payload, 'PUT', "{$this->name}/{$order_ref}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function delete($order_ref): \stdClass
    {
        $this->logger->notice("VirtualAccount Service::Updating Virtual Account. [{$order_ref}]");

        self::startRecording();
        $response = $this->request([ 'status' => 'inactive'], 'POST', "{$this->name}/{$order_ref}");
        self::setResponseTime();
        return $response;
    }
}
