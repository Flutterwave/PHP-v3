<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\SubaccountEventHandler;
use Flutterwave\Entities\Payload;
use GuzzleHttp\Exception\GuzzleException;

class CollectionSubaccount extends Service
{
    private SubaccountEventHandler $eventHandler;

    private string $name = 'subaccounts';

    private array $requiredParams = [
        'account_bank', 'account_number',
        'business_name', 'split_value',
        'business_mobile','business_email', 'country'
    ];
    private array $requiredParamsUpdate = [ 'split_value'];

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $endpoint = $this->name;
        $this->url = $this->baseUrl . '/' . $endpoint;
        $this->eventHandler = new SubaccountEventHandler();
    }

    public function confirmPayload(Payload $payload): array
    {
        foreach ($this->requiredParams as $param) {
            if (! $payload->has($param)) {
                $msg = "The required parameter {$param} is not present in payload";
                $this->logger->error("Subaccount Service::" . $msg);
                throw new \InvalidArgumentException("Subaccount Service:" . $msg);
            }
        }

        return $payload->toArray();
    }

    /**
     * @param  Payload $payload
     * @return \stdClass
     * @throws GuzzleException
     */
    public function create(Payload $payload): \stdClass
    {
        $this->logger->notice('Subaccount Service::Creating new Collection Subaccount.');
        $body = $this->confirmPayload($payload);
        $this->logger->notice('Subaccount Service::Payload Confirmed.');
        $this->eventHandler::startRecording();
        $response = $this->request($body, 'POST');

        if (isset($response->status) && $response->status === 'success') {
            $this->logger->notice('Subaccount Service::Collection Subaccount created successfully.');
        } else {
            $this->logger->error('Subaccount Service::Collection Subaccount creation failed.');
        }

        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @return \stdClass
     * @throws GuzzleException
     */
    public function list(): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return \stdClass
     * @throws GuzzleException
     */
    public function get(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string  $id
     * @param  Payload $payload
     * @return \stdClass
     * @throws GuzzleException
     */
    public function update(string $id, Payload $payload): \stdClass
    {
        foreach ($this->requiredParamsUpdate as $param) {
            if (! $payload->has($param)) {
                $msg = "The required parameter {$param} is not present in payload";
                $this->logger->error("Subaccount Service::" . $msg);
                throw new \InvalidArgumentException("Subaccount Service:" . $msg);
            }
        }

        $payload = $payload->toArray();
        $this->eventHandler::startRecording();
        $response = $this->request($payload, 'PUT', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return \stdClass
     * @throws GuzzleException
     */
    public function delete(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'DELETE', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }
}
