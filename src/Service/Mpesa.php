<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\MpesaEventHandler;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class Mpesa extends Service implements Payment
{
    use Charge;

    public const TYPE = 'mpesa';
    private MpesaEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl.'/'.$endpoint.'?type=';
        $this->eventHandler = new MpesaEventHandler();
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
        $this->logger->notice('Charging via Mpesa ...');

        $number = $payload->get('customer')->toArray()['phone_number'];
        $currency = $payload->get('currency');

        if (\is_null($number)) {
            $this->logger->warning('Phone parameter required for the request.');
            throw new \InvalidArgumentException('Phone parameter required for the request. ');
        }

        if (! \is_null($currency) && $currency !== 'KES') {
            $this->logger->warning("The currency {$currency} is not supported for Mpesa transaction.");
            throw new \InvalidArgumentException("The currency {$currency} is not supported for Mpesa transaction.");
        }

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['country']);
        unset($body['address']);

        MpesaEventHandler::startRecording();
        $request = $this->request($body, 'POST', self::TYPE);
        MpesaEventHandler::setResponseTime();

        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws \Exception
     */
    private function handleAuthState(\stdClass $response, array $payload): array
    {
        $mode = $response->data->auth_model;
        $this->logger->info("Mpesa Auth Mode: {$mode}");
        return [
            'status' => $response->data->status,
            'transactionId' => $response->data->id,
            'dev_instruction' => 'The customer should authorize the payment on their Phones via the Mpesa. status is pending',
            'instruction' => 'Please kindly authorize the payment on your Mobile phone',
            'mode' => $mode,
        ];
    }
}
