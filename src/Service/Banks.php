<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Unirest\Exception;

class Banks extends Service
{
    private string $name = "banks";
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function getByCountry(string $country = "NG"): \stdClass
    {
        $this->logger->notice("Bank Service::Retrieving banks in country:($country).");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name."/$country");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getBranches(string $id): \stdClass
    {
        $this->logger->notice("Bank Service::Retrieving Bank Branches bank_id:($id).");
        self::startRecording();
        $response = $this->request(null,'GET', $this->name."/$id/branches");
        self::setResponseTime();
        return $response;
    }
}