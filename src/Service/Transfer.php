<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\TransferEventHandler;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Flutterwave\Traits\Group\Charge;
use stdClass;
use Unirest\Exception;

class Transfer extends Service implements Payment
{
    use Charge;
    const TYPE = 'transfers';
    private TransferEventHandler $eventHandler;
    private string $name = 'transfers';
    private array $requiredParamsFee = [
        "amount", "currency"
    ];
    private array $requiredParamsRate = [
        "amount", "destination_currency". "source_currency"
    ];
    function __construct(Config $config)
    {
        parent::__construct($config);

        $endpoint = "transfers";
        $this->url  = $this->baseUrl."/".$endpoint;
        $this->eventHandler = new TransferEventHandler();
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload)
    {
        $tx_ref = $payload->get("tx_ref");
        $this->logger->info("Transfer Service::Initiating Transfer....{$tx_ref}");
        if($this->checkPayloadIsValid($payload, "account_details"))
        {
            return $this->charge($payload);
        }
    }

    /**
     * @param Payload $payload
     * @return stdClass
     * @throws Exception
     */
    public function charge(\Flutterwave\Payload $payload): stdClass
    {
        $additionalData = $payload->get("otherData");
        $tx_ref = $payload->get("tx_ref");

        if(!array_key_exists("narration", $additionalData)){
           throw new \InvalidArgumentException("Please pass the parameter 'narration' in the additionalData array");
        }
        $this->logger->notice("Transfer Service::Transferring to account ...");

        $payload->set("reference", $tx_ref);

        $payload = $payload->toArray();

        unset($payload['tx_ref']);

        $this->eventHandler::startRecording();
        $response = $this->request($payload, 'POST');
        $this->eventHandler::setResponseTime();
        return $response; //TODO: change to return an Array

    }

    public function save(callable $callback)
    {
        // TODO: Implement save() method.
    }

    /**
     * @param string|null $transactionId
     * @return stdClass
     * retry a previously failed transfer.
     *
     * @throws Exception
     */
    public function retry(?string $transactionId): stdClass
    {
        $this->checkTransactionId($transactionId);
        $this->logger->notice("Transfer Service::Retrieving Settlement [$transactionId].");
        $this->eventHandler::startRecording();
        $response = $this->request(null,'POST', $this->name."/$transactionId/retries");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function createBulk(Payload $payload): stdClass
    {
        if(!$payload->has('bulk_data')){
            $this->logger->error("Transfer Service::Bulk Payload is empty. Pass a filled array");
            throw new \InvalidArgumentException("Transfer Service::Bulk Payload is currently empty. Pass a filled array");
        }

        $body =  $payload->toArray();
        $this->logger->notice("Transfer Service::Creating a Bulk Transfer.");
        self::startRecording();
        $response = $this->request($body,'POST', "bulk-transfers");
        $this->logger->notice("Transfer Service::Created a Bulk Transfer Successfully.");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Transfer id:($id)");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name."/$id");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getAll(): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving all Transfers");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getFee(array $params = []): stdClass
    {
        foreach ($this->requiredParamsFee as $param){
            if(!array_key_exists($param, $params)){
                $this->logger->error("Transfer Service::the following param is required to get transfer fee: $param");
                throw new \InvalidArgumentException("Transfer Service::the following param is required to get transfer fee: $param");
            }
        }

        $query = http_build_query($params);
        $this->logger->notice("Transfer Service::Retrieving Transfer Fee");
        self::startRecording();
        $response = $this->request(null,'GET', "/fee?$query");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getRetry(string $id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Transfer id:($id)");
        self::startRecording();
        $response = $this->request(null,'GET', "/$id/retries");
        $this->logger->info("Transfer Service::Transfer retry attempts retrieved.");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getBulk(string $batch_id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Bulk Transfer id:($batch_id)");
        self::startRecording();
        $response = $this->request(null,'GET', "?batch_id=$batch_id");
        $this->logger->info("Transfer Service::Bulk Transfer retrieved.");
        self::setResponseTime();
        return $response;
    }

    public function getRates(array $params): stdClass
    {
        foreach ($this->requiredParamsRate as $param){
            if(!array_key_exists($param, $params)){
                $this->logger->error("Transfer Service::the following param is required to get transfer rate: $param");
                throw new \InvalidArgumentException("Transfer Service::the following param is required to get transfer rate: $param");
            }
        }

        $query = http_build_query($params);
        $logData = json_encode($params);
        $this->logger->notice("Transfer Service::Retrieving Transfer Rate data:($logData)");
        self::startRecording();
        $response = $this->request(null,'GET', "?$query");
        $this->logger->info("Transfer Service::Transfer rate retrieved.");
        self::setResponseTime();
        return $response;
    }

}

