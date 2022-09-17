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
    private array $requiredParams = [ "account_bank", "account_number", "business_name", "split_value", "business_mobile","business_email", "country" ];
    private array $requiredParamsUpdate = [ "split_value"];
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $endpoint = $this->name;
        $this->url  = $this->baseUrl."/".$endpoint;
        $this->eventHandler = new SubaccountEventHandler();
    }

    public function confirmPayload(Payload $payload): array
    {

        foreach($this->requiredParams as $param){
            if(!$payload->has($param))
            {
                $this->logger->error("Subaccount Service::The required parameter $param is not present in payload");
                throw new \InvalidArgumentException("Subaccount Service:The required parameter $param is not present in payload");
            }
        }

        return $payload->toArray();
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
    public function update(string $id, Payload $payload): \stdClass
    {
        foreach($this->requiredParamsUpdate as $param){
            if(!$payload->has($param))
            {
                $this->logger->error("Subaccount Service::The required parameter $param is not present in payload");
                throw new \InvalidArgumentException("Subaccount Service:The required parameter $param is not present in payload");
            }
        }

        $payload = $payload->toArray();
        $this->eventHandler::startRecording();
        $response = $this->request($payload,'PUT', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function delete(string $id): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null,'DELETE', "/{$id}");
        $this->eventHandler::setResponseTime();
        return $response;
    }
}
