<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\TkEventHandler;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class TokenizedCharge extends Service implements Payment
{
    use Charge;

    public const TYPE = 'tokenized-charges';
    private static string $name = 'tokenize';
    private TkEventHandler $eventHandler;
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $endpoint = "tokenized-{$this->getEndpoint()}";
        $this->url = $this->baseUrl.'/'.$endpoint;
        $this->eventHandler = new TkEventHandler();
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload)
    {
        $this->logger->notice('Tokenize Service::Initiating Card Payment...');
        if (! $this->checkPayloadIsValid($payload, 'token')) {
            $msg = 'Tokenize Service::Please enter token parameter within the additionalData array';
            $this->logger->notice($msg);
            throw new \InvalidArgumentException($msg);
        }

        $this->logger->notice('Tokenize Service::Payload Confirmed...');
        return $this->charge($payload);
    }

    /**
     * @throws Exception
     */
    public function charge(\Flutterwave\Payload $payload): array
    {
        # format the customer object to extract the first_name and the last name.
        $customer = $payload->get('customer')->toArray();
        $fullname = $customer['fullname'];
        $names = explode(' ', $fullname);
        $first_name = $names[0];
        $last_name = $names[1];

        $payload->set('first_name', $first_name);
        $payload->set('last_name', $last_name);
        $payload = $payload->toArray();
        $body = $payload;

        TkEventHandler::startRecording();
        $request = $this->request($body, 'POST');
        TkEventHandler::setResponseTime();
        return $this->handleAuthState($request, $payload);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    private function handleAuthState(\stdClass $response, $payload): array
    {
        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $data['tx_ref'] = $tx_ref;
            $data['transanctionId'] = $transactionId;
            $data['status'] = $response->data->status;
            $this->logger->notice("Tokenize Service::Retrieved Status...{$data['status']}");
        }
        return $data ?? [ 'status' => 'Pending' ];
    }
}
