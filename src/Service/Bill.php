<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;

class Bill extends Service
{
    use EventTracker;

    protected ?array $categories = null;
    private string $name = 'bill-categories';
    private array $requiredParams = [
        'country',
        'customer_id',
        'amount',
        'reference',
        'biller_code',
        'item_code'
    ];

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $this->categories = include __DIR__ . '/../Util/bill_categories.php';
    }

    /**
     * @throws ClientExceptionInterface
     * @deprecated Use `getBillCategories()` instead.
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
     * This retrieves the categories of bills that can be paid for.
     */
    public function getBillCategories(): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving Top Categories.');
        self::startRecording();
        $response = $this->request(null, 'GET', "top-".$this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * Retrieve items under a specific biller code.
     */
    public function getBillerItems(string $biller_code = null): \stdClass 
    {
        if(is_null($biller_code)) {
            $msg = "The required parameter" . $biller_code . " is not present in payload";
            $this->logger->error("Bill Payment Service::$msg");
            throw new \InvalidArgumentException("Bill Payment Service:$msg");
        }

        $this->logger->notice('Bill Payment Service::Retrieving items under biller '. $biller_code);
        self::startRecording();
        $response = $this->request(null, 'GET', sprintf('billers/%s/items', $biller_code));
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     * @deprecated Use `validateCustomerInfo()` instead.
     */
    public function validateService(string $item_code): \stdClass
    {
        $this->logger->notice('Bill Payment Service::Retrieving all Plans.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "bill-item/{$item_code}/validate");
        self::setResponseTime();
        return $response;
    }

    public function validateCustomerInfo(\Flutterwave\Payload $payload): \stdClass
    {
        $payload = $payload->toArray();

        foreach (['biller_code', 'customer', 'item_code'] as $param) {
            if (! array_key_exists($param, $payload)) {
                $msg = "The required parameter ". $param. " is not present in payload";
                $this->logger->error("Bill Payment Service::$msg");
                throw new \InvalidArgumentException("Bill Payment Service:$msg");
            }
        }

        $code = $payload['biller_code'];
        $customer = $payload['customer'];
        $customer = $customer[0] == '+' ? substr($customer, 1) : $customer;
        $item_code = $payload['item_code'];
    
        $this->logger->notice('Bill Payment Service::Retrieving all Plans.');
        self::startRecording();
        $response = $this->request(null, 'GET', sprintf("bill-items/{$item_code}/validate?code=%s&customer=%s", $code, $customer));
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function createPayment(\Flutterwave\Payload $payload): \stdClass
    {
        $payload = $payload->toArray();
        foreach ($this->requiredParams as $param) {
            if (! array_key_exists($param, $payload)) {
                $msg = "The required parameter ". $param. " is not present in payload";
                $this->logger->error("Bill Payment Service::$msg");
                throw new \InvalidArgumentException("Bill Payment Service:$msg");
            }
        }

        $body = $payload;

        $biller_code = $payload['biller_code'];
        $item_code = $payload['item_code'];

        $this->logger->notice('Bill Payment Service::Creating a Bill Payment.');
        self::startRecording();
        $response = $this->request($body, 'POST', sprintf('billers/%s/items/%s/payment', $biller_code, $item_code));
        $this->logger->notice('Bill Payment Service::Created a Bill Payment Successfully.');
        self::setResponseTime();
        return $response;
    }

    public function createBulkPayment(array $bulkPayload): \stdClass
    {
        if (empty($bulkPayload)) {
            $msg = 'Bulk Payload is empty. Pass a filled array';
            $this->logger->error('Bill Payment Service::' . $msg);
            throw new \InvalidArgumentException('Bill Payment Service::' . $msg);
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
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
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
