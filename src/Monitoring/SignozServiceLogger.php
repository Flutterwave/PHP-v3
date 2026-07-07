<?php

namespace Flutterwave\Monitoring;

use Flutterwave\Helper\EnvVariables;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

class SignozServiceLogger
{
    private const BASE_URL = 'https://signozservice-prod.f4b-flutterwave.com';
    private const MERCHANT_INFO = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/mercinfo?PBFPubKey=';
    private const API_KEY = '%%SIGNOZ_API_KEY%%';
    private const LIBRARY = 'PHP';

    // --- Health check ---
    private const HEALTH_PATH = '/health/ready';
    private const HEALTH_CACHE_TTL = 60;   // seconds a successful health check is trusted

    // --- Circuit breaker ---
    private const CB_FAILURE_THRESHOLD = 3;    // consecutive failures before opening
    private const CB_OPEN_TTL = 120;  // seconds the circuit stays open (cooldown)
    private const CB_FAILURES_KEY = 'signoz:cb:failures';
    private const CB_OPEN_UNTIL_KEY = 'signoz:cb:open_until';
    private const HEALTH_OK_KEY = 'signoz:health:ok_until';

    // --- Retry / backoff (503 only) ---
    private const MAX_ATTEMPTS = 3;     // total attempts (1 initial + 2 retries)
    private const BASE_DELAY_MS = 200;   // backoff base
    private const MAX_DELAY_MS = 1500;  // per-retry delay cap
    private const ERROR_MESSAGE_MAX_LENGTH = 4096;
    private const ERROR_STACKTRACE_MAX_LENGTH = 16384;

    private static bool $appCreatedSent = false;

    // In-process fallbacks when no PSR cache is configured.
    private static int $staticFailureCount = 0;
    private static int $staticOpenUntil = 0;
    private static int $staticHealthyUntil = 0;

    private string $apiKey;

    private ClientInterface $httpClient;
    private ?CacheInterface $cache;
    private string $libraryVersion;
    private bool $debug = false;

    private ?array $lastResponse = null;
    private ?string $lastResponseBody = null;
    private ?int $lastResponseStatus = null;
    private ?string $lastResponseReason = null;

    private ?string $appId = null;

    private string $publicKey;

    private string $environment;

    private ?array $defaultTraceContext = null;
    private array $traceContextsByReference = [];

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

        if (self::API_KEY === '%%SIGNOZ_API_KEY%%') {
            $this->apiKey = $this->env('SIGNOZ_API_KEY', 'IuUnO5cwI6Ta1JO/LEFUsMyz1AH3FNzW');
        }

        $debugValue = $this->env('SIGNOZ_DEBUG', '0');
        $this->debug = $debugValue === true || $debugValue === 'true' || $debugValue === '1';
    }

    public function getAppId()
    {
        if (!empty($this->appId)) {
            return $this->appId;
        }

        $cacheKey = sprintf('signoz:app_id:%s', hash('sha256', $this->publicKey));

        if ($this->cache !== null) {
            try {
                $cachedAppId = $this->cache->get($cacheKey, null);
                if (!empty($cachedAppId) && is_string($cachedAppId)) {
                    $this->appId = $this->normalizeAppId($cachedAppId);
                    return $this->appId;
                }
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }

        // $merchantId = $this->getMerchantId($this->publicKey);
        // if (!empty($merchantId)) {
        //     $this->appId = $this->normalizeAppId($merchantId);
        //     return $this->appId;
        // }
        return $this->normalizeAppId($this->publicKey);
    }

    public function getCurrentEnvironment(): string
    {
        return $this->environment !== 'production' ? 'sandbox' : 'production';
    }

    public function setDefaultTraceContext(?array $traceContext): void
    {
        $this->defaultTraceContext = $traceContext;
    }

    public function getDefaultTraceContext(): ?array
    {
        return $this->defaultTraceContext;
    }


    private function traceContextCacheKey(string $reference): string
    {
        return sprintf('signoz:trace_ctx:%s', $this->normalizeReference($reference));
    }

    public function setTraceContextForReference(string $reference, ?array $traceContext): void
    {
        $key = $this->normalizeReference($reference);

        if ($traceContext === null) {
            unset($this->traceContextsByReference[$key]);
            if ($this->cache !== null) {
                try {
                    $this->cache->delete($this->traceContextCacheKey($reference));
                } catch (\Throwable $e) {
                }
            }
            return;
        }

        $this->traceContextsByReference[$key] = $traceContext;

        if ($this->cache !== null) {
            try {
                // TTL ~ payment session lifetime; 1h is a reasonable ceiling
                $this->cache->set($this->traceContextCacheKey($reference), $traceContext, 3600);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }
    }

    public function getTraceContextForReference(string $reference): ?array
    {
        $key = $this->normalizeReference($reference);

        if (isset($this->traceContextsByReference[$key])) {
            return $this->traceContextsByReference[$key];
        }

        if ($this->cache !== null) {
            try {
                $ctx = $this->cache->get($this->traceContextCacheKey($reference));
                if (is_array($ctx)) {
                    $this->traceContextsByReference[$key] = $ctx; // warm local
                    return $ctx;
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    public function getMerchantId(string $publicKey)
    {
        try {
            $response = $this->httpClient->request('GET', self::MERCHANT_INFO . $publicKey, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!empty($result) && isset($result['mn'])) {
                return $result['mn'];
            }
        } catch (\Throwable $e) {
            // observability must never break payments
        }
        return null;
    }

    public function trackAppCreated(
        string $publicKey
    ): void {
        $cacheKey = sprintf('signoz:app_created:%s', hash('sha256', $publicKey));

        if (self::$appCreatedSent) {
            return;
        }

        if ($this->cache !== null) {
            try {
                if ($this->cache->has($cacheKey)) {
                    self::$appCreatedSent = true;
                    return;
                }
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }

        $this->send('app.created', [
            'client_id' => null,
            'public_key' => $publicKey,
            'library' => self::LIBRARY,
            'library_version' => $this->libraryVersion,
        ]);

        if ($this->cache !== null) {
            try {
                $this->cache->set($cacheKey, true);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }

        self::$appCreatedSent = true;
    }

    public function trackRequestSent(
        string $appId,
        string $environment,
        string $method,
        string $reference,
        string $path,
        ?array $traceContext = null
    ): void {
        $safeReference = $this->normalizeReference($reference);

        $payload = [
            'app_id' => $this->normalizeAppId($appId),
            'environment' => $environment,
            'api_version' => EnvVariables::VERSION,
            'library' => self::LIBRARY,
            'library_version' => $this->libraryVersion,
            'method' => $method,
            'path' => $path,
            'reference' => $safeReference,
        ];

        $payload['trace_context'] = $traceContext ?? $this->resolveTraceContext($this->defaultTraceContext, $safeReference);
        if ($payload['trace_context'] === null) {
            unset($payload['trace_context']);
        }

        $cacheKey = sprintf(
            'signoz:request_sent:%s',
            $safeReference
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
        float $fee,
        ?array $traceContext = null
    ): void {
        $payload = [
            'app_id' => $this->normalizeAppId($appId),
            'reference' => $reference,
            'library' => self::LIBRARY,
            'currency' => $currency,
            'amount' => $amount,
            'fee' => $fee,
            'method' => $method,
        ];

        $payload['trace_context'] = $traceContext ?? $this->resolveTraceContext($this->defaultTraceContext, $reference);
        if ($payload['trace_context'] === null) {
            unset($payload['trace_context']);
        }

        $this->send('app.transaction', $payload);
    }

    public function trackError(
        string $appId,
        string $errorCode,
        string $errorMessage,
        ?array $traceContext = null,
        ?string $stackTrace = null
    ): void {
        $payload = [
            'app_id' => $this->normalizeAppId($appId),
            'library' => self::LIBRARY,
            'library_version' => $this->libraryVersion,
            'error_code' => $errorCode,
            'error_message' => $this->truncateValue($errorMessage, self::ERROR_MESSAGE_MAX_LENGTH),
        ];

        if ($stackTrace !== null && $stackTrace !== '') {
            $payload['error_stacktrace'] = $this->truncateValue($stackTrace, self::ERROR_STACKTRACE_MAX_LENGTH);
        }

        $payload['trace_context'] = $traceContext ?? $this->resolveTraceContext($this->defaultTraceContext, $appId);
        if ($payload['trace_context'] === null) {
            unset($payload['trace_context']);
        }

        $this->send('app.error', $payload);
    }

    private function resolveTraceContext(?array $defaultTraceContext, string $reference): ?array
    {
        if ($defaultTraceContext !== null) {
            return $defaultTraceContext;
        }

        return $this->traceContextsByReference[$reference] ?? null;
    }

    private function truncateValue(string $value, int $maxLength): string
    {
        if (mb_strlen($value) <= $maxLength) {
            return $value;
        }

        return mb_substr($value, 0, $maxLength);
    }

    private function send(string $eventName, array $data): void
    {
        try {
            // 1. Circuit breaker gate: if open, drop the event immediately.
            if ($this->isCircuitOpen()) {
                return;
            }

            // 2. Health gate: verify /health/ready (cached for HEALTH_CACHE_TTL).
            //    When the circuit has just moved out of cooldown, this acts as
            //    the half-open probe before real traffic resumes.
            if (!$this->isServiceHealthy()) {
                $this->recordFailure();
                return;
            }

            $this->sendWithRetry($eventName, $data);
        } catch (\Throwable $e) {
            // observability must never break payments
        }
    }

    private function sendWithRetry(string $eventName, array $data): void
    {
        $body = [
            'name' => $eventName,
            'data' => $data,
            'timestamp' => gmdate('Y-m-d\TH:i:s.000\Z'),
        ];

        $secret = self::API_KEY === '%%SIGNOZ_API_KEY%%' ? $this->apiKey : self::API_KEY;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                $response = $this->httpClient->request('POST', self::BASE_URL . '/events', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-api-key' => $secret
                    ],
                    'json' => $body,

                    // fire-and-forget-ish
                    'timeout' => 2,
                    'connect_timeout' => 1,
                ]);

                $responseBody = (string) $response->getBody();
                $this->maybeUpdateAppIdFromResponse($eventName, $responseBody);
                $this->storeLastResponse($response, $responseBody);
                $this->recordSuccess();
                return;
            } catch (RequestException $e) {
                $response = $e->getResponse();
                $statusCode = $response !== null ? $response->getStatusCode() : 0;

                if ($response !== null) {
                    $responseBody = (string) $response->getBody();
                    $this->maybeUpdateAppIdFromResponse($eventName, $responseBody);
                    $this->storeLastResponse($response, $responseBody);
                }

                // Only 503 (service temporarily unavailable) is retried.
                if ($statusCode === 503 && $attempt < self::MAX_ATTEMPTS) {
                    $this->backoffSleep($attempt);
                    continue;
                }

                $this->recordFailure();
                return;
            } catch (\Throwable $e) {
                // Network/transport errors: count as a failure, never throw.
                $this->recordFailure();
                return;
            }
        }

        // Exhausted retries (all 503s).
        $this->recordFailure();
    }

    /**
     * Exponential backoff with full jitter:
     * sleep for random(0, min(MAX_DELAY, BASE * 2^(attempt-1))).
     */
    private function backoffSleep(int $attempt): void
    {
        $ceilingMs = min(
            self::MAX_DELAY_MS,
            self::BASE_DELAY_MS * (2 ** ($attempt - 1))
        );

        try {
            $delayMs = random_int(0, $ceilingMs);
        } catch (\Throwable $e) {
            $delayMs = $ceilingMs;
        }

        usleep($delayMs * 1000);
    }

    /**
     * GET /health/ready and require {"status":"ok"}.
     * A passing check is cached so we don't probe on every event.
     */
    private function isServiceHealthy(): bool
    {
        $now = time();

        // Trust a recent successful health check.
        if ($this->cache !== null) {
            try {
                if ($this->cache->has(self::HEALTH_OK_KEY)) {
                    return true;
                }
            } catch (\Throwable $e) {
                // fall through to live probe
            }
        } elseif (self::$staticHealthyUntil > $now) {
            return true;
        }

        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . self::HEALTH_PATH, [
                'headers' => [
                    'x-api-key' => self::API_KEY,
                ],
                'timeout' => 1,
                'connect_timeout' => 1,
            ]);

            if ($response->getStatusCode() !== 200) {
                return false;
            }

            $result = json_decode($response->getBody()->getContents(), true);

            $healthy = is_array($result)
                && isset($result['status'])
                && $result['status'] === 'ok';

            if ($healthy) {
                if ($this->cache !== null) {
                    try {
                        $this->cache->set(self::HEALTH_OK_KEY, true, self::HEALTH_CACHE_TTL);
                    } catch (\Throwable $e) {
                        // observability must never break payments
                    }
                } else {
                    self::$staticHealthyUntil = $now + self::HEALTH_CACHE_TTL;
                }
            }

            return $healthy;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // --- Circuit breaker state -------------------------------------------

    private function isCircuitOpen(): bool
    {
        $now = time();

        if ($this->cache !== null) {
            try {
                $openUntil = (int) ($this->cache->get(self::CB_OPEN_UNTIL_KEY, 0));
                return $openUntil > $now;
            } catch (\Throwable $e) {
                // fall back to in-process state
            }
        }

        return self::$staticOpenUntil > $now;
    }

    private function recordSuccess(): void
    {
        self::$staticFailureCount = 0;
        self::$staticOpenUntil = 0;

        if ($this->cache !== null) {
            try {
                $this->cache->delete(self::CB_FAILURES_KEY);
                $this->cache->delete(self::CB_OPEN_UNTIL_KEY);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }
    }

    private function recordFailure(): void
    {
        $failures = ++self::$staticFailureCount;

        if ($this->cache !== null) {
            try {
                $failures = (int) $this->cache->get(self::CB_FAILURES_KEY, 0) + 1;
                $this->cache->set(self::CB_FAILURES_KEY, $failures, self::CB_OPEN_TTL * 2);
            } catch (\Throwable $e) {
                // keep in-process count
            }
        }

        if ($failures >= self::CB_FAILURE_THRESHOLD) {
            $this->openCircuit();
        }
    }

    private function openCircuit(): void
    {
        $openUntil = time() + self::CB_OPEN_TTL;

        self::$staticOpenUntil = $openUntil;
        self::$staticFailureCount = 0;

        if ($this->cache !== null) {
            try {
                $this->cache->set(self::CB_OPEN_UNTIL_KEY, $openUntil, self::CB_OPEN_TTL);
                $this->cache->delete(self::CB_FAILURES_KEY);

                // Invalidate the cached health status so the next attempt
                // after cooldown re-probes /health/ready (half-open behavior).
                $this->cache->delete(self::HEALTH_OK_KEY);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }

        self::$staticHealthyUntil = 0;
    }

    private function normalizeAppId(string $appId): string
    {
        return preg_replace('/\s+/', '-', trim($appId)) ?? $appId;
    }

    /**
     * @param string $key
     * @param mixed  $default
     */
    private function env(string $key, $default = null): string
    {
        return $_ENV[$key] ?? $default;
    }

    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    private function maybeUpdateAppIdFromResponse(string $eventName, string $responseBody): void
    {
        if ($eventName !== 'app.created') {
            return;
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded) || empty($decoded['app_id'])) {
            return;
        }

        $this->appId = $this->normalizeAppId((string) $decoded['app_id']);

        if ($this->cache !== null) {
            try {
                $cacheKey = sprintf('signoz:app_id:%s', hash('sha256', $this->publicKey));
                $this->cache->set($cacheKey, $this->appId, 86400);
            } catch (\Throwable $e) {
                // observability must never break payments
            }
        }
    }

    private function storeLastResponse(ResponseInterface $response, ?string $responseBody = null): void
    {
        $this->lastResponseStatus = $response->getStatusCode();
        $this->lastResponseReason = $response->getReasonPhrase();
        $this->lastResponseBody = $responseBody ?? (string) $response->getBody();

        $decoded = json_decode($this->lastResponseBody, true);
        $this->lastResponse = is_array($decoded)
            ? $decoded
            : [
                'status' => $this->lastResponseStatus,
                'reason' => $this->lastResponseReason,
                'body' => $this->lastResponseBody,
            ];

        if ($this->debug) {
            error_log('SignozServiceLogger response: ' . json_encode($this->lastResponse));
        }
    }

    private function normalizeReference(string $reference): string
    {
        $normalized = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim($reference));

        if ($normalized === null) {
            return $reference;
        }

        return trim($normalized, '-');
    }
}
