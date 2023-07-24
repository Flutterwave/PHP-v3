<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\TransferEventHandler;
use Flutterwave\Entities\Payload;
use Flutterwave\Traits\Group\Charge;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

class Transfer extends Service implements Payment
{
    use Charge;

    public const TYPE = 'transfers';
    private TransferEventHandler $eventHandler;
    private string $name = 'transfers';
    private array $requiredParamsFee = [
        'amount', 'currency',
    ];
    private array $requiredParamsRate = [
        'amount', 'destination_currency' . 'source_currency',
    ];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = 'transfers';
        $this->url = $this->baseUrl . '/' . $endpoint;
        $this->eventHandler = new TransferEventHandler($config);
    }

    /**
     * @param  Payload $payload
     * @return array
     * @throws ClientExceptionInterface
     */
    public function initiate(Payload $payload): array
    {
        $tx_ref = $payload->get('tx_ref');
        $this->logger->info("Transfer Service::Initiating Transfer....$tx_ref");
        if ($this->checkPayloadIsValid($payload, 'account_details')) {
            return $this->charge($payload);
        }
        throw new InvalidArgumentException('Please check your payload');
    }

    /**
     * @param  Payload $payload
     * @return array
     * @throws ClientExceptionInterface
     */
    public function charge(Payload $payload): array
    {
        $additionalData = $payload->get('otherData');
        $tx_ref = $payload->get('tx_ref');

        if (! array_key_exists('narration', $additionalData)) {
            throw new InvalidArgumentException("Please pass the parameter 'narration' in the additionalData array");
        }
        $this->logger->notice('Transfer Service::Transferring to account ...');

        $payload->set('reference', $tx_ref);

        $payload = $payload->toArray('account');

        unset($payload['tx_ref']);
        unset($payload['address']);

        $this->eventHandler::startRecording();
        $response = $this->request($payload, 'POST');
        $this->eventHandler::setResponseTime();
        return $this->handleInitiationResponse($response); //TODO: change to return an Array
    }

    private function handleInitiationResponse(stdClass $data): array
    {
        $root = $data->data;
        return [
            'id' => $root->id,
            'account_number' => $root->account_number,
            'bank_code' => $root->bank_code,
            'full_name' => $root->full_name,
            'currency' => $root->currency,
        //            'debit_currency' => $root->debit_currency,
            'reference' => $root->reference,
            'amount' => $root->amount,
            'status' => $root->status,
            'bank_name' => $root->bank_name
        ];
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @param  string|null $transactionId
     * @return stdClass
     * retry a previously failed transfer.
     *
     * @throws ClientExceptionInterface
     */
    public function retry(?string $transactionId): stdClass
    {
        $this->checkTransactionId($transactionId);
        $this->logger->notice("Transfer Service::Retrieving Settlement [$transactionId].");
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'POST', $this->name . "/$transactionId/retries");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  Payload $payload
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function createBulk(Payload $payload): stdClass
    {
        if (! $payload->has('bulk_data')) {
            $msg = 'Bulk Payload is empty. Pass a filled array';
            $this->logger->error('Transfer Service::' . $msg);
            throw new InvalidArgumentException('Transfer Service::' . $msg);
        }

        $body = $payload->toArray();
        $this->logger->notice('Transfer Service::Creating a Bulk Transfer.');
        $this->eventHandler::startRecording();
        $response = $this->request($body, 'POST', 'bulk-transfers');
        $this->logger->notice('Transfer Service::Created a Bulk Transfer Successfully.');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function get(string $id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Transfer id:($id)");
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', $this->name . "/$id");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function getAll(): stdClass
    {
        $this->logger->notice('Transfer Service::Retrieving all Transfers');
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', $this->name);
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  array $params
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function getFee(array $params = []): stdClass
    {
        foreach ($this->requiredParamsFee as $param) {
            if (! array_key_exists($param, $params)) {
                $msg = "the following param is required to get transfer fee: $param";
                $this->logger->error("Transfer Service::$msg");
                throw new InvalidArgumentException("Transfer Service::$msg");
            }
        }

        $query = http_build_query($params);
        $this->logger->notice('Transfer Service::Retrieving Transfer Fee');
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/fee?$query");
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string $id
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function getRetry(string $id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Transfer id:($id)");
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "/$id/retries");
        $this->logger->info('Transfer Service::Transfer retry attempts retrieved.');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  string $batch_id
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function getBulk(string $batch_id): stdClass
    {
        $this->logger->notice("Transfer Service::Retrieving Bulk Transfer id:($batch_id)");
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "?batch_id=$batch_id");
        $this->logger->info('Transfer Service::Bulk Transfer retrieved.');
        $this->eventHandler::setResponseTime();
        return $response;
    }

    /**
     * @param  array $params
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function getRates(array $params): stdClass
    {
        foreach ($this->requiredParamsRate as $param) {
            if (! array_key_exists($param, $params)) {
                $msg = "the following param is required to get transfer rate: $param";
                $this->logger->error("Transfer Service::$msg");
                throw new InvalidArgumentException("Transfer Service::$msg");
            }
        }

        $query = http_build_query($params);
        $logData = json_encode($params);
        $this->logger->notice("Transfer Service::Retrieving Transfer Rate data:($logData)");
        $this->eventHandler::startRecording();
        $response = $this->request(null, 'GET', "?$query");
        $this->logger->info('Transfer Service::Transfer rate retrieved.');
        $this->eventHandler::setResponseTime();
        return $response;
    }
}
