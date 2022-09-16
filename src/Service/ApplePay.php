<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\ApplePayEventHandler;
use Flutterwave\Helper\Config;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class ApplePay extends Service implements Payment
{
    use Charge;

    const TYPE = 'applepay';
    private ApplePayEventHandler $eventHandler;

    public function __construct(Config $config)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url  = $this->baseUrl."/".$endpoint."?type=";
        $this->eventHandler = new ApplePayEventHandler();
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload): array
    {
        return $this->charge($payload);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function charge(\Flutterwave\Payload $payload): array
    {
        $this->logger->notice("Apple Service::Started Charging Process ...");

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['country']);
        unset($body['address']);

        ApplePayEventHandler::startRecording();
        $request = $this->request($body,'POST', self::TYPE);
        ApplePayEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback)
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws \Exception
     */
    private function handleAuthState(\stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger] );
    }
}