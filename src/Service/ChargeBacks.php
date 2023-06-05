<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Unirest\Exception;

class ChargeBacks extends Service
{
    use EventTracker;

    private string $name = 'chargebacks';
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws Exception
     */
    public function get(string $flw_ref): \stdClass
    {
        $this->logger->notice("ChargeBacks Service::Retrieving Chargeback.[flw_ref:{$flw_ref}]");
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "?flw_ref={$flw_ref}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function getAll(array $filters = []): \stdClass
    {
        $query = http_build_query($filters) ?? '';
        $this->logger->notice('ChargeBacks Service::Retrieving Chargebacks.[all]');
        self::startRecording();
        $response = $this->request(null, 'GET', $this->name . "?{$query}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function accept(string $chargeback_id): \stdClass
    {
        $this->logger->notice("ChargeBacks Service::Accepting Chargeback [{$chargeback_id}].");
        self::startRecording();
        $response = $this->request([ 'action' => 'accept'], 'PUT', $this->name . "/{$chargeback_id}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function decline(string $chargeback_id): \stdClass
    {
        $this->logger->notice("ChargeBacks Service::Declining Chargeback [{$chargeback_id}].");
        self::startRecording();
        $response = $this->request([ 'action' => 'decline'], 'PUT', $this->name . "/{$chargeback_id}");
        self::setResponseTime();
        return $response;
    }
}
