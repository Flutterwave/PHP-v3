<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

class Banks extends Service
{
    use EventTracker;

    private string $name = 'banks';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getByCountry(string $country = 'NG'): stdClass
    {
        $this->logger->notice("Bank Service::Retrieving banks in country:({$country}).");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/{$country}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getBranches(string $id): stdClass
    {
        $this->logger->notice("Bank Service::Retrieving Bank Branches bank_id:({$id}).");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/{$id}/branches");
        self::setResponseTime();
        return $response;
    }
}
