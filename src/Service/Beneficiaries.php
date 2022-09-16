<?php

namespace Flutterwave\Service;

use Flutterwave\EventHandlers\EventTracker;
use Flutterwave\EventHandlers\RecipientEventHandler;
use Flutterwave\Helper\Config;
use Unirest\Exception;

class Beneficiaries extends Service
{
    use EventTracker;
    private string $name = "beneficiaries";
    private array $requiredParams = [
        'account_bank','account_number','beneficiary_name'
    ];
    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function create(\Flutterwave\Payload $payload): \stdClass
    {
        $payload = $payload->toArray();
        foreach ($this->requiredParams as $param){
            if(!array_key_exists($param, $payload)){
                $this->logger->error("Beneficiaries Service::The required parameter $param is not present in payload");
                throw new \InvalidArgumentException("Beneficiaries Service:The required parameter $param is not present in payload");
            }
        }

        $body = $payload;

        $this->logger->notice("Beneficiaries Service::Creating a Beneficiary.");
        self::startRecording();
        $response = $this->request($body,'POST', $this->name);
        $this->logger->notice("Beneficiaries Service::Created a Beneficiary Successfully.");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->logger->notice("Beneficiaries Service::Retrieving all Beneficiaries.");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name);
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $id): \stdClass
    {
        $this->logger->notice("Beneficiaries Service::Retrieving a Beneficiary.");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name."/$id");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function delete(string $id): \stdClass
    {
        $this->logger->notice("Beneficiaries Service::Delete a Beneficiary.");
        self::startRecording();
        $response = $this->request(null,'DELETE', $this->name."/$id");
        self::setResponseTime();
        return $response;
    }
}
