<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\UssdEventHandler;
use Flutterwave\Entities\Payload;
use Flutterwave\Traits\Group\Charge;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;

class Ussd extends Service implements Payment
{
    use Charge;

    public const TYPE = 'ussd';
    protected ?string $type = null;
    private UssdEventHandler $eventHandler;
    private string $country = 'NG';
    private array $requiredParam = [
        'account_number','account_bank',
    ];
    private array $supported_banks = [
        '044' => 'Access Bank',
        '050' => 'Ecobank',
        '070' => 'Fidelity',
        '011' => 'First Bank of Nigeria',
        '214' => 'First city monument bank',
        '058' => 'Guranteed Trust Bank',
        '030' => 'Heritage Bank',
        '082' => 'Keystone Bank',
        '221' => 'Stanbic IBTC bank',
        '232' => 'Sterling bank',
        '032' => 'Union bank',
        '033' => 'United bank for Africa',
        '215' => 'United Bank',
        '090110' => 'VFD microfinance bank',
        '035' => 'Wema bank',
        '057' => 'Zenith bank',
    ];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl . '/' . $endpoint . '?type=';
        $this->eventHandler = new UssdEventHandler($config);
    }

    /**
     * @param  Payload $payload
     * @return array
     * @throws ClientExceptionInterface
     */
    public function initiate(Payload $payload): array
    {
        $this->logger->info('Ussd Service::Initiated Ussd Charge');
        return $this->charge($payload);
    }

    /**
     * @param  Payload $payload
     * @return array
     * @throws ClientExceptionInterface
     */
    public function charge(Payload $payload): array
    {
        $otherData = $payload->get('otherData');

        if (empty($otherData)) {
            $msg = "Please pass the missing parameters 'account_number' and 'account_bank'";
            $this->logger->error("Ussd Service::{$msg}");
            throw new \InvalidArgumentException("Ussd Service::{$msg}");
        }

        foreach ($this->requiredParam as $param) {
            if (! array_key_exists($param, $otherData)) {
                $msg = "Please pass the missing parameter '{$param}'";
                $this->logger->error("Ussd Service::{$msg}");
                throw new \InvalidArgumentException("Ussd Service::{$msg}");
            }
        }

        $bank = $otherData['account_bank'];

        if (! array_key_exists($bank, $this->supported_banks)) {
            $msg = 'We do not support your bank. please kindly use another. ';
            $this->logger->error('USSD Service:' . $msg);
            throw new \InvalidArgumentException('USSD Service:' . $msg);
        }

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['country']);
        unset($body['address']);

        $this->eventHandler::startRecording();
        $this->logger->info('Ussd Service::Generating Ussd Code');
        $request = $this->request($body, 'POST', self::TYPE);
        $this->logger->info('Ussd Service::Generated Ussd Code Successfully');
        $this->eventHandler::setResponseTime();

        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @param  \stdClass $response
     * @param  array     $payload
     * @return array
     * @throws \Exception
     */
    private function handleAuthState(\stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger]);
    }
}
