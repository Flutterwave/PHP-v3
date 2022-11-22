<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Exception;
use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\BankTransferEventHandler;
use Flutterwave\Payload;
use Flutterwave\Traits\Group\Charge;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class BankTransfer extends Service implements Payment
{
    use Charge;

    public const TYPE = 'bank_transfer';
    private bool $isPermanent = false;
    private BankTransferEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl.'/'.$endpoint.'?type=';
        $this->eventHandler = new BankTransferEventHandler();
    }

    public function makePermanent(): void
    {
        if (! $this->isPermanent) {
            $this->isPermanent = true;
        }
    }

    /**
     * @param Payload $payload
     * @return array
     *
     * @throws GuzzleException
     */
    public function initiate(Payload $payload): array
    {
        return $this->charge($payload);
    }

    /**
     * @param Payload $payload
     * @return array
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function charge(Payload $payload): array
    {
        $this->logger->notice('Bank Transfer Service::Generating Account for Customer Transfer ...');

        $payload->set('is_permanent', (int) $this->isPermanent);
        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        BankTransferEventHandler::startRecording();
        $request = $this->request($body, 'POST', self::TYPE);
        BankTransferEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @param stdClass $response
     * @param array $payload
     * @return array
     * @throws Exception
     */
    private function handleAuthState(stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger]);
    }
}
