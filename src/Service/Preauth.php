<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\PreEventHandler;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class Preauth extends Service implements Payment
{
    use Charge;

    private CardPayment $cardService;
    private PreEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $this->cardService = new CardPayment($config);
        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl.'/'.$endpoint;
        $this->eventHandler = new PreEventHandler();
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload): ?array
    {
        $this->logger->info('Preauth Service::Updated Payload...');
        $payload->set('preauthorize', 1);
        $payload->set('usesecureauth', 1);
        $this->logger->info('Preauth Service::Communicating to Card Service...');
        return $this->charge($payload);
    }

    /**
     * @throws Exception
     */
    public function charge(\Flutterwave\Payload $payload): ?array
    {
        PreEventHandler::startRecording();
        $response = $this->cardService->initiate($payload);
        PreEventHandler::setResponseTime();
        return $response;
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws Exception
     */
    public function capture(string $flw_ref, string $method = 'card', string $amount = '0'): array
    {
        $method = strtolower($method);
        switch ($method) {
            case 'paypal':
                $data = [
                    'flw_ref' => $flw_ref,
                ];
                $this->logger->info("Preauth Service::Capturing PayPal Payment with FLW_REF:{$flw_ref}...");
                $response = $this->request($data, 'POST', '/paypal-capture');
                break;
            default:
                $data = ['amount' => $amount];
                $this->logger->info("Preauth Service::Capturing Payment with FLW_REF:{$flw_ref}...");
                $response = $this->request($data, 'POST', "/{$flw_ref}/capture");
                break;
        }

        $data['message'] = null;

        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $flw_ref = $response->data->flw_ref;
            $data['transactionId'] = $transactionId;
            $data['tx_ref'] = $tx_ref;
            $data['flw_ref'] = $flw_ref;
            $data['message'] = $response->message;
        }

        $msg = $data['message'] ?? 'Charge Capturing Failed!';
        $this->logger->info("Preauth Service::{$msg}...");

        return $data ?? [ 'message' => 'Charge Capturing Failed!'];
    }

    /**
     * @throws Exception
     */
    public function void(string $flw_ref, string $method = 'card'): array
    {
        $method = strtolower($method);
        switch ($method) {
            case 'paypal':
                $data = [
                    'flw_ref' => $flw_ref,
                ];
                $this->logger->info("Preauth Service::Voiding Payment with FLW_REF:{$flw_ref}...");
                PreEventHandler::startRecording();
                $response = $this->request($data, 'POST', '/paypal-void');
                PreEventHandler::setResponseTime();
                break;
            default:
                PreEventHandler::startRecording();
                $this->logger->info("Preauth Service::Voiding Payment with FLW_REF:{$flw_ref}...");
                PreEventHandler::setResponseTime();
                $response = $this->request(null, 'POST', "/{$flw_ref}/void");
                break;
        }

        $data['message'] = null;

        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $flw_ref = $response->data->flw_ref;
            $data['transactionId'] = $transactionId;
            $data['tx_ref'] = $tx_ref;
            $data['flw_ref'] = $flw_ref;
            $data['message'] = $response->message;
        }

        $msg = $data['message'] ?? 'Charge Voiding Failed!';
        $this->logger->info("Preauth Service::{$msg}...");

        return $data ?? [ 'message' => 'Charge Voiding Failed!'];
    }

    /**
     * @throws Exception
     */
    public function refund(string $flw_ref): array
    {
        $this->logger->info("Preauth Service::Refunding Payment with FLW_REF:{$flw_ref}...");
        PreEventHandler::startRecording();
        $response = $this->request(null, 'POST', "/{$flw_ref}/refund");
        PreEventHandler::setResponseTime();
        $data['message'] = null;
        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $flw_ref = $response->data->flw_ref;
            $data['transactionId'] = $transactionId;
            $data['tx_ref'] = $tx_ref;
            $data['flw_ref'] = $flw_ref;
            $data['message'] = $response->message;
        }

        $msg = $data['message'] ?? 'Charge Refund Failed!';
        $this->logger->info("Preauth Service::{$msg}...");

        return $data ?? [ 'message' => 'Charge Refund Failed!'];
    }
}
