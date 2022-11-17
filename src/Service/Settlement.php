<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Unirest\Exception;

class Settlement extends Service
{
    use EventTracker;
    private string $name = 'settlements';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function get(string $id): \stdClass
    {
        $this->logger->notice("Settlement Service::Retrieving Settlement [{$id}].");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name."/{$id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->logger->notice('Settlement Service::Retrieving all Settlements.');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        self::setResponseTime();
        return $response;
    }
}
