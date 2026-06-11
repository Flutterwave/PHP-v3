<?php

namespace Flutterwave\Monitoring;

use GuzzleHttp\ClientInterface;
use Psr\SimpleCache\CacheInterface;

class SignozServiceLogger
{
    private const BASE_URL = 'https://signozservice-prod.f4b-flutterwave.com';
    private const MERCHANT_INFO = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/mercinfo?PBFPubKey=';
    private const API_KEY  = '%%SIGNOZ_API_KEY%%';
    private const LIBRARY  = 'PHP';

    private static bool $appCreatedSent = false;

    private ClientInterface $httpClient;
    private ?CacheInterface $cache;
    private string $libraryVersion;

    private string $appId;

    private string $publicKey;

    private string $environment;

    public function __construct(
        ClientInterface $httpClient,
        string $publicKey,
        string $environment,
        ?CacheInterface $cache = null,
        string $libraryVersion = '1.0.0'
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->libraryVersion = $libraryVersion;
        $this->publicKey = $publicKey;
        $this->environment = $environment;
    }

    public function getAppId() {
            if (!empty($this->appId)) {
                return $this->appId;
            }

            $merchantId = $this->getMerchantId($this->publicKey);
            if (!empty($merchantId)) {
                $this->appId = $merchantId;
                return $this->appId;
            }
        return $this->publicKey;
    }

    public function getCurrentEnvironment(): string
    {
        return $this->environment !== 'production' ? 'sandbox' : 'production';
    }

    public function getMerchantId(string $publicKey) {
        try {
            $response = $this->httpClient->request('GET', self::MERCHANT_INFO . $this->publicKey, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if(!empty($result) && isset($result['mn'])) {
                return $result['mn'];
            }
        } catch (\Throwable $e) {
            // observability must never break payments
        }
        return null;
    }

    public function trackAppCreated(
        string $publicKey,
        string $merchantId
    ): void {
        if (self::$appCreatedSent) {
            return;
        }
        $this->send('app.created', [
            'app_id'          => $merchantId,
            'public_key'      => $publicKey,
            'library'         => self::LIBRARY,
            'library_version' => $this->libraryVersion,
        ]);
    }

    public function trackRequestSent(
        string $appId,
        string $environment,
        string $method,
        string $reference,
        string $path
    ): void {
        $payload = [
            'app_id'          => $appId,
            'environment'     => $environment,
            'api_version'     => 'v3',
            'library_version' => $this->libraryVersion,
            'method'          => $method,
            'path'            => $path,
            'reference'       => $reference,
        ];

        $cacheKey = sprintf(
            'signoz:request_sent:%s',
            $reference
        );

        if ($this->cache !== null) {

            try {
                if ($this->cache->has($cacheKey)) {
                    return;
                }

                $this->cache->set($cacheKey, true, 300);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }

        $this->send('request.sent', $payload);
    }

    public function trackTransaction(
        string $appId,
        string $reference,
        string $currency,
        float $amount,
        string $method,
        float $fee
    ): void {
        $this->send('app.transaction', [
            'app_id'    => $appId,
            'reference' => $reference,
            'currency'  => $currency,
            'amount'    => $amount,
            'fee'       => $fee,
            'method'    => $method,
        ]);
    }

    public function trackError(
        string $appId,
        string $errorCode,
        string $errorMessage
    ): void {
        $this->send('app.error', [
            'app_id'          => $appId,
            'library'         => self::LIBRARY,
            'library_version' => $this->libraryVersion,
            'error_code'      => $errorCode,
            'error_message'   => $errorMessage,
        ]);
    }

    private function send(string $eventName, array $data): void
    {
        try {
            $this->httpClient->request('POST', self::BASE_URL . '/events', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key'    => self::API_KEY,
                ],
                'json' => [
                    'name'      => $eventName,
                    'data'      => $data,
                    'timestamp' => gmdate('Y-m-d\TH:i:s.000\Z'),
                ],

                // fire-and-forget-ish
                'timeout' => 2,
                'connect_timeout' => 1,
            ]);
        } catch (\Throwable $e) {
            // observability must never break payments
        }
    }
}