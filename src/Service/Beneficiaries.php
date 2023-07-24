<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Flutterwave\Entities\Payload;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

class Beneficiaries extends Service
{
    use EventTracker;

    private string $name = 'beneficiaries';
    private array $requiredParams = [
        'account_bank','account_number','beneficiary_name',
    ];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @param  Payload $payload
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function create(Payload $payload): stdClass
    {
        $payload = $payload->toArray();

        if (array_key_exists('customer', $payload)) {
            $msg = 'The required parameter customer Object is not present in payload';
            $this->logger->error('Beneficiaries Service::' . $msg);
            throw new InvalidArgumentException('Beneficiaries Service:' . $msg);
        }

        foreach ($this->requiredParams as $param) {
            if (! array_key_exists($param, $payload)) {
                $msg = 'The required parameter {$param} is not present in payload';
                $this->logger->error("Beneficiaries Service::$msg");
                throw new InvalidArgumentException("Beneficiaries Service:$msg");
            }
        }

        $body = $payload;

        $this->logger->notice('Beneficiaries Service::Creating a Beneficiary.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name);
        $this->logger->notice('Beneficiaries Service::Created a Beneficiary Successfully.');
        self::setResponseTime();
        return $response;
    }

    /**
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function list(): stdClass
    {
        $this->logger->notice('Beneficiaries Service::Retrieving all Beneficiaries.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function get(string $id): stdClass
    {
        $this->logger->notice('Beneficiaries Service::Retrieving a Beneficiary.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function delete(string $id): stdClass
    {
        $this->logger->notice('Beneficiaries Service::Delete a Beneficiary.');
        self::startRecording();
        $response = $this->request(null, 'DELETE', $this->name . "/{$id}");
        self::setResponseTime();
        return $response;
    }
}
