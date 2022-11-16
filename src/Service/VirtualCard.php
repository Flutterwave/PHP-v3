<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Flutterwave\Payload;
use InvalidArgumentException;
use Unirest\Exception;

class VirtualCard extends Service
{
    use EventTracker;
    private string $name = 'virtual-cards';
    private array $requiredParams = [ 'currency', 'amount', 'first_name', 'last_name', 'date_of_birth','email', 'phone', 'title', 'gender' ];
    private array $requiredParamsFund = ['debit_currency','amount'];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    public function confirmPayload(Payload $payload): array
    {
        foreach ($this->requiredParams as $param) {
            if (! $payload->has($param)) {
                $this->logger->error("VirtualCard Service::The required parameter {$param} is not present in payload");
                throw new InvalidArgumentException("The required parameter {$param} is not present in payload");
            }
        }

        return $payload->toArray();
    }

    /**
     * @throws Exception
     */
    public function create(Payload $payload): \stdClass
    {
        $this->logger->notice('VirtualCard Service::Creating new Virtual Card.');
        $body = $this->confirmPayload($payload);
        $this->logger->notice('VirtualCard Service::Payload Confirmed.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $id): \stdClass
    {
        $this->logger->notice("VirtualCard Service::Retrieving Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name."/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->logger->notice('VirtualCard Service::Retrieving all Virtual Cards.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function fund(string $id, array $data): \stdClass
    {
        foreach ($this->requiredParamsFund as $param) {
            if (! array_key_exists($param, $data)) {
                $this->logger->error("Misc Service::The following parameter is missing to check balance history: {$param}");
                throw new \InvalidArgumentException("The following parameter is missing to check balance history: {$param}");
            }
        }

        $this->logger->notice("VirtualCard Service::Funding Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request($data, 'POST', $this->name."/{$id}/fund");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function withdraw(string $id, string $amount = '0'): \stdClass
    {
        $this->logger->notice("VirtualCard Service::Withdrawing from Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request([ 'amount' => $amount ], 'POST', $this->name."/{$id}/withdraw");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function block(string $id): \stdClass
    {
        $this->logger->notice("VirtualCard Service::Blocking Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name."/{$id}/status/block");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function unblock(string $id): \stdClass
    {
        $this->logger->notice("VirtualCard Service::Unblocking Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name."/{$id}/status/unblock");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function terminate(string $id): \stdClass
    {
        $this->logger->notice("VirtualCard Service::Terminating Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name."/{$id}/terminate");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getTransactions(string $id, array $options = ['index' => 0, 'size' => 20]): \stdClass
    {
        $query = http_build_query($options);
        $this->logger->notice("VirtualCard Service::Retrieving transaction for Virtual Card [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name."/{$id}/transactions?{$query}");
        self::setResponseTime();
        return $response;
    }
}
