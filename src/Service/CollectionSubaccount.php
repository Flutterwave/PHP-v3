<?php
namespace Flutterwave\Service;
use Flutterwave\EventHandlers\SubaccountEventHandler;
use Flutterwave\Helper\Config;
use Flutterwave\Payload;
use Unirest\Exception;

class CollectionSubaccount extends Service
{
    private SubaccountEventHandler $eventHandler;
    private string $name = "subaccounts";
    private array $requiredParams = [ "account_bank", "account_number", "business_name", "split_value", "business_mobile" ];
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $endpoint = $this->name;
        $this->url  = $this->baseUrl."/".$endpoint;
        $this->eventHandler = new SubaccountEventHandler();
    }

    public function confirmPayload(Payload $payload): array
    {
        $payload = $payload->toArray();

        foreach($this->requiredParams as $param){
            if(array_key_exists($param, $payload))
            {
                $this->logger->error("Subaccount Service::The required parameter $param is not present in payload");
                throw new \InvalidArgumentException("The required parameter $param is not present in payload");
            }
        }

        return $payload;
    }

    /**
     * @throws Exception
     */
    public function create(Payload $payload): \stdClass
    {
        $this->logger->notice("Subaccount Service::Creating new Collection Subaccount.");
        $body = $this->confirmPayload($payload);
        $this->logger->notice("Subaccount Service::Payload Confirmed.");
        $this->eventHandler::startRecording();
        $response = $this->request($body,'POST');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null,'GET');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null,'GET', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function update(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null,'PUT', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function delete(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null,'DELETE', "/{$id}/transactions");
        $this->eventHandler::setResponseTime();
        return $response;
    }
}
