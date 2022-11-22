<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\PayoutSubaccoutEventHandler;
use Flutterwave\Payload;
use Unirest\Exception;

class PayoutSubaccount extends Service
{
    private string $name = 'payout-subaccounts';
    private array $requiredParams = [ 'email', 'mobilenumber','country' ];
    private PayoutSubaccoutEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $endpoint = $this->name;
        $this->url = $this->baseUrl.'/'.$endpoint;
        $this->eventHandler = new PayoutSubaccoutEventHandler();
    }

    public function confirmPayload(Payload $payload): array
    {
        //TODO: throw exceptions on missing params
        $customer = $payload->get('customer')->toArray();
        $email = $customer['email'];
        $phone = $customer['phone_number'];
        $fullname = $customer['fullname'];
        $country = $payload->get('country');
        $this->logger->notice('PSA Service::Confirming Payload...');
        return [
            'email' => $email,
            'mobilenumber' => $phone,
            'account_name' => $fullname,
            'country' => $country,
        ];
    }

    /**
     * @throws Exception
     */
    public function create(Payload $payload): \stdClass
    {
        $this->logger->notice('PSA Service::Creating new Payout Subaccount.');
        $body = $this->confirmPayload($payload);
        $this->logger->notice('PSA Service::Payload Confirmed.');
        $this->eventHandler::startRecording();
        $response = $this->request($body, 'POST');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function list(): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $account_reference): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function update(string $account_reference, Payload $payload): \stdClass
    {
        if (! $payload->has('account_name') || ! $payload->has('mobilenumber') || ! $payload->has('email')) {
            $msg = "Please pass the required paramters:'account_name','mobilenumber',and 'email' ";
            $this->logger->error($msg);
            throw new \InvalidArgumentException($msg);
        }

        $this->eventHandler::startRecording();
        $response = $this->request($payload->toArray(), 'PUT', "/{$account_reference}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function fetchTransactions(string $account_reference): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/transactions");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function fetchAvailableBalance(string $account_reference, string $currency = 'NGN'): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/balances?currency={$currency}");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @throws Exception
     */
    public function fetchStaticVirtualAccounts(string $account_reference, string $currency = 'NGN'): \stdClass
    {
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/static-account?currency={$currency}");
        $this->eventHandler::setResponseTime();
        return $response;
    }
}
