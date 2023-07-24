<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventTracker;
use Psr\Http\Client\ClientExceptionInterface;

class Misc extends Service
{
    use EventTracker;

    private string $name = 'balances';
    private array $requiredParamsHistory = [
        'from','to','currency',
    ];
    private array $requiredParamsAccountResolve = [
        'account_number','account_bank',
    ];
    private array $requiredParamsUserBackground = [
        'entity', 'type',
    ];
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getWallet($currency): \stdClass
    {
        $this->logger->info("Misc Service::Getting {$currency} wallet balance.");
        self::startRecording();
        $response = $this->request(null, 'GET', "balances/{$currency}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getWallets(): \stdClass
    {
        $this->logger->info('Misc Service::Getting wallet balance(s).');
        self::startRecording();
        $response = $this->request(null, 'GET', 'balances');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getBalanceHistory(array $queryParams): \stdClass
    {
        foreach ($this->requiredParamsHistory as $param) {
            if (! array_key_exists($param, $queryParams)) {
                $msg = "The following parameter is missing to check balance history: {$param}";
                $this->logger->error("Misc Service::$msg");
                throw new \InvalidArgumentException($msg);
            }
        }

        $query = http_build_query($queryParams);
        $this->logger->info('Misc Service::Getting wallet balance(s).');
        self::startRecording();
        $response = $this->request(null, 'GET', "wallet/statement?{$query}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function resolveAccount(\Flutterwave\Payload $payload): \stdClass
    {
        $payload = $payload->toArray();
        foreach ($this->requiredParamsAccountResolve as $param) {
            if (! array_key_exists($param, $payload)) {
                $this->logger->error("Misc Service::The following parameter is missing to resolve account: {$param}");
                throw new \InvalidArgumentException("The following parameter is missing to resolve account: {$param}");
            }
        }

        $this->logger->info('Misc Service::Resolving Account Details.');
        self::startRecording();
        $response = $this->request($payload, 'POST', 'accounts/resolve');
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function resolveBvn(string $bvn): \stdClass
    {
        $this->logger->info('Misc Service::Resolving BVN.');
        self::startRecording();
        $response = $this->request(null, 'GET', "kyc/bvns/{$bvn}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function resolveCardBin(string $bin): \stdClass
    {
        $this->logger->info('Misc Service::Resolving Card BIN.');
        self::startRecording();
        $response = $this->request(null, 'GET', "card-bins/{$bin}");
        self::setResponseTime();
        return $response;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function userBackgroundCheck(array $data): \stdClass
    {
        foreach ($this->requiredParamsUserBackground as $param) {
            if (! array_key_exists($param, $data)) {
                $msg = "The following parameter is missing to check user background: {$param}";
                $this->logger->error("Misc Service::$msg");
                throw new \InvalidArgumentException($msg);
            }
        }

        $this->logger->info('Misc Service::Initiating User Background Check.');
        self::startRecording();
        $response = $this->request(null, 'GET', 'fraud-check');
        self::setResponseTime();
        return $response;
    }
}
