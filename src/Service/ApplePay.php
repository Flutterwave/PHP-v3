<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\ApplePayEventHandler;
use Flutterwave\Payload;
use Flutterwave\Traits\Group\Charge;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ApplePay extends Service implements Payment
{
    use Charge;

    public const TYPE = 'applepay';
    private ApplePayEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl.'/'.$endpoint.'?type=';
        $this->eventHandler = new ApplePayEventHandler();
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     */
    public function initiate(Payload $payload): array
    {
        return $this->charge($payload);
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     */
    public function charge(Payload $payload): array
    {
        $this->logger->notice('Apple Service::Started Charging Process ...');

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['country']);
        unset($body['address']);

        ApplePayEventHandler::startRecording();
        $request = $this->request($body, 'POST', self::TYPE);
        ApplePayEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    private function handleAuthState(stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger]);
    }
}
