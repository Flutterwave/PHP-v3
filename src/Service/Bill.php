<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Unirest\Exception;

class Bill extends Service
{
    use EventTracker;
    protected ?array $categories = null;
    private string $name = 'bill-categories';
    private array $requiredParams = [
        'country','customer','amount','type','reference',
    ];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $this->categories = require __DIR__ . '/../Util/bill_categories.php';
    }

    /**
     * @throws Exception
     */
    public function getCategories(): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving all Categories.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function validateService(string $item_code): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving all Plans.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name."bill-item/{$item_code}/validate");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function createPayment(\Flutterwave\Payload $payload): \stdClass
    {
        $payload =
        $payload = $payload->toArray();
        foreach ($this->requiredParams as $param) {
            if (! array_key_exists($param, $payload)) {
                $this->logger->error("Bill Payment Service::The required parameter {$param} is not present in payload");
                throw new \InvalidArgumentException("Bill Payment Service:The required parameter {$param} is not present in payload");
            }
        }

        $body = $payload;

        $this->logger->notice('Bill Payment Service::Creating a Bill Payment.');
        self::startRecording();
        $response = $this->request($body, 'POST', 'bills');
        $this->logger->notice('Bill Payment Service::Created a Bill Payment Successfully.');
        self::setResponseTime();
        return $response;
    }

    public function createBulkPayment(array $bulkPayload): \stdClass
    {
        if (empty($bulkPayload)) {
            $this->logger->error('Bill Payment Service::Bulk Payload is empty. Pass a filled array');
            throw new \InvalidArgumentException('Bill Payment Service::Bulk Payload is currently empty. Pass a filled array');
        }

        $body = $bulkPayload;
        $this->logger->notice('Bill Payment Service::Creating a Bulk Bill Payment.');
        self::startRecording();
        $response = $this->request($body, 'POST', $this->name);
        $this->logger->notice('Bill Payment Service::Created a Bulk Bill Payment Successfully.');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getBillStatus(string $reference): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving Bill Payment Status');
        self::startRecording();
        $response = $this->request(null, 'GET', "bills/{$reference}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getBillPayments(): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving Bill Payment');
        self::startRecording();
        $response = $this->request(null, 'GET', 'bills');
        self::setResponseTime();
        return $response;
    }
}
