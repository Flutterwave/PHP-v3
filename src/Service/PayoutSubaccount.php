<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\PayoutSubaccoutEventHandler;
use Flutterwave\Payload;
use Psr\Http\Client\ClientExceptionInterface;
use Flutterwave\EventHandlers\EventTracker;
use stdClass;

class PayoutSubaccount extends Service
{
    use EventTracker;
    private string $name = 'payout-subaccounts';
    private array $requiredParams = [ 'email', 'mobilenumber','country' ];

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
        $endpoint = $this->name;
        $this->url = $this->baseUrl . '/' . $endpoint;
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
     * @param  Payload $payload
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function create(Payload $payload): stdClass
    {
        $this->logger->notice('PSA Service::Creating new Payout Subaccount.');
        $body = $this->confirmPayload($payload);
        $this->logger->notice('PSA Service::Payload Confirmed.');
        self::startRecording();
        $response = $this->request($body, 'POST');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function list(): stdClass
    {
        self::startRecording();
        $response = $this->request(null, 'GET');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function get(string $account_reference): \stdClass
    {
        self::startRecording();
        $response = $this->request(null, 'GET', "/$account_reference");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function update(string $account_reference, Payload $payload): \stdClass
    {
        if (! $payload->has('account_name') || ! $payload->has('mobilenumber') || ! $payload->has('email')) {
            $msg = "Please pass the required paramters:'account_name','mobilenumber',and 'email' ";
            $this->logger->error($msg);
            throw new \InvalidArgumentException($msg);
        }

        self::startRecording();
        $response = $this->request($payload->toArray(), 'PUT', "/{$account_reference}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function fetchTransactions(string $account_reference): \stdClass
    {
        self::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/transactions");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function fetchAvailableBalance(string $account_reference, string $currency = 'NGN'): \stdClass
    {
        self::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/balances?currency={$currency}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function fetchStaticVirtualAccounts(string $account_reference, string $currency = 'NGN'): stdClass
    {
        self::startRecording();
        $response = $this->request(null, 'GET', "/{$account_reference}/static-account?currency={$currency}");
        self::setResponseTime();
        return $response;
    }
}
