<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\Payment;

use Flutterwave\EventHandlers\BankTransferEventHandler;
use Flutterwave\Helper\Config;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class BankTransfer extends Service implements Payment
{
    use Charge;

    const TYPE = 'bank_transfer';
    private bool $isPermanent = false;
    private BankTransferEventHandler $eventHandler;

    function __construct(Config $config)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url  = $this->baseUrl."/".$endpoint."?type=";
        $this->eventHandler = new BankTransferEventHandler();
    }

    public function makePermanent()
    {
        if(!$this->isPermanent){
            $this->isPermanent = true;
        }
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
        $this->logger->notice("Bank Transfer Service::Generating Account for Customer Transfer ...");

        $payload->set('is_permanent', (int) $this->isPermanent);
        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        BankTransferEventHandler::startRecording();
        $request = $this->request($body,'POST', self::TYPE);
        BankTransferEventHandler::setResponseTime();
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

