<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;

class PaymentPlan extends Service
{
    use EventTracker;

    private array $requiredParams = [
        'amount','name','interval','duration',
    ];
    private string $name = 'payment-plans';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function create(\Flutterwave\Payload $payload): \stdClass
    {
        $payload = $payload->toArray();
        foreach ($this->requiredParams as $param) {
            if (! array_key_exists($param, $payload)) {
                $msg = "The required parameter {$param} is not present in payload";
                $this->logger->error("Payment Plan Service::" . $msg);
                throw new \InvalidArgumentException("Payment Plan Service:" . $msg);
            }
        }

        $body = $payload;

        $this->logger->notice('Payment Plan Service::Creating a Plan.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name);
        $this->logger->notice('Payment Plan Service::Created a Plan Successfully.');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function get(string $id): \stdClass
    {
        $this->logger->notice("Payment Plan Service::Retrieving a Plan ({$id}).");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function list(): \stdClass
    {
        $this->logger->notice('Payment Plan Service::Retrieving all Plans.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function update(string $id, \Flutterwave\Payload $payload): \stdClass
    {
        if (! $payload->has('amount') && ! $payload->has('status')) {
            $msg = "Payment Plan Service(Action:Update):Please pass the required params: 'amount' and 'status'";
            $this->logger->error($msg);
            throw new \InvalidArgumentException($msg);
        }

        $this->logger->notice("Payment Plan Service::Updating Plan id:({$id})");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name . "/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function cancel(string $id): \stdClass
    {
        $this->logger->notice("Payment Plan Service::Canceling Plan id:({$id})");
        self::startRecording();
        $response = $this->request(null, 'PUT', $this->name . "/{$id}/cancel");
        self::setResponseTime();
        return $response;
    }
}
