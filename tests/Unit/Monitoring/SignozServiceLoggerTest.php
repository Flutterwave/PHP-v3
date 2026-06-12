<?php

declare(strict_types=1);

namespace Flutterwave\Test\Unit\Monitoring;

use Flutterwave\Monitoring\SignozServiceLogger;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

class SignozServiceLoggerTest extends TestCase
{
    private const BASE_URL      = 'https://signozservice-prod.f4b-flutterwave.com';
    private const EVENTS_URL    = self::BASE_URL . '/events';
    private const HEALTH_URL    = self::BASE_URL . '/health/ready';
    private const MERC_INFO_URL = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/mercinfo?PBFPubKey=';

    protected function setUp(): void
    {
        $this->resetStaticState();
    }

    protected function tearDown(): void
    {
        $this->resetStaticState();
    }

    // -----------------------------------------------------------------
    // Health check gate
    // -----------------------------------------------------------------

    public function testEventIsSentWhenServiceIsHealthy(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            if ($uri === self::HEALTH_URL) {
                return $this->healthyResponse();
            }

            return new Response(200);
        });

        $logger = $this->makeLogger($httpClient);
        $logger->trackError('app-1', 'ERR_TEST', 'Something went wrong');

        $this->assertCount(2, $calls);
        $this->assertSame(['GET', self::HEALTH_URL], [$calls[0]['method'], $calls[0]['uri']]);
        $this->assertSame(['POST', self::EVENTS_URL], [$calls[1]['method'], $calls[1]['uri']]);

        // Health probe must carry the API key header.
        $this->assertArrayHasKey('x-api-key', $calls[0]['options']['headers']);

        // Event payload sanity check.
        $this->assertSame('app.error', $calls[1]['options']['json']['name']);
        $this->assertSame('app-1', $calls[1]['options']['json']['data']['app_id']);
    }

    public function testHealthCheckIsCachedAcrossEvents(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            return $uri === self::HEALTH_URL ? $this->healthyResponse() : new Response(200);
        });

        $logger = $this->makeLogger($httpClient);
        $logger->trackError('app-1', 'ERR_ONE', 'first');
        $logger->trackError('app-1', 'ERR_TWO', 'second');

        // 1 health probe + 2 event POSTs — the second event reuses the
        // cached health result instead of probing again.
        $this->assertCount(3, $calls);
        $this->assertSame(self::HEALTH_URL, $calls[0]['uri']);
        $this->assertSame(self::EVENTS_URL, $calls[1]['uri']);
        $this->assertSame(self::EVENTS_URL, $calls[2]['uri']);
    }

    public function testEventIsDroppedWhenHealthStatusIsNotOk(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            if ($uri === self::HEALTH_URL) {
                return new Response(200, [], json_encode([
                    'status'       => 'degraded',
                    'dependencies' => ['redis' => 'down'],
                ]));
            }

            $this->fail('No event should be sent when the service is unhealthy.');
        });

        $logger = $this->makeLogger($httpClient);
        $logger->trackError('app-1', 'ERR_TEST', 'Something went wrong');

        // Only the health probe — no POST to /events.
        $this->assertCount(1, $calls);
        $this->assertSame(self::HEALTH_URL, $calls[0]['uri']);
        $this->assertSame(1, $this->getStaticValue('staticFailureCount'));
    }

    // -----------------------------------------------------------------
    // 503 retry with backoff
    // -----------------------------------------------------------------

    public function testRetriesUpToMaxAttemptsOn503(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            if ($uri === self::HEALTH_URL) {
                return $this->healthyResponse();
            }

            throw $this->serviceUnavailableException();
        });

        $logger = $this->makeLogger($httpClient);
        $logger->trackError('app-1', 'ERR_TEST', 'Something went wrong');

        // 1 health probe + 3 POST attempts (MAX_ATTEMPTS).
        $this->assertCount(4, $calls);
        $this->assertSame(self::HEALTH_URL, $calls[0]['uri']);
        foreach (array_slice($calls, 1) as $call) {
            $this->assertSame(self::EVENTS_URL, $call['uri']);
        }

        // Exhausted retries count as one breaker failure.
        $this->assertSame(1, $this->getStaticValue('staticFailureCount'));
    }

    public function testDoesNotRetryOnNon503Errors(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            if ($uri === self::HEALTH_URL) {
                return $this->healthyResponse();
            }

            throw new RequestException(
                'Unprocessable Entity',
                new Request('POST', self::EVENTS_URL),
                new Response(422)
            );
        });

        $logger = $this->makeLogger($httpClient);
        $logger->trackError('app-1', 'ERR_TEST', 'Something went wrong');

        // 1 health probe + exactly 1 POST attempt — 422 is not retried.
        $this->assertCount(2, $calls);
        $this->assertSame(1, $this->getStaticValue('staticFailureCount'));
    }

    // -----------------------------------------------------------------
    // Circuit breaker
    // -----------------------------------------------------------------

    public function testCircuitOpensAfterConsecutiveFailuresAndBlocksSends(): void
    {
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) {
            // Every health probe fails -> every send records a failure.
            throw new RequestException(
                'Connection refused',
                new Request('GET', self::HEALTH_URL)
            );
        });

        $logger = $this->makeLogger($httpClient);

        // Three failures reach CB_FAILURE_THRESHOLD and open the circuit.
        $logger->trackError('app-1', 'ERR_1', 'first');
        $logger->trackError('app-1', 'ERR_2', 'second');
        $logger->trackError('app-1', 'ERR_3', 'third');

        $this->assertCount(3, $calls);
        $this->assertGreaterThan(time(), $this->getStaticValue('staticOpenUntil'));

        // Circuit is open: this send must make zero HTTP calls.
        $logger->trackError('app-1', 'ERR_4', 'fourth');
        $this->assertCount(3, $calls);
    }

    public function testSuccessfulSendResetsCircuitBreakerState(): void
    {
        $shouldFail = true;
        $calls = [];
        $httpClient = $this->mockHttpClient($calls, function (string $method, string $uri) use (&$shouldFail) {
            if ($uri === self::HEALTH_URL) {
                if ($shouldFail) {
                    throw new RequestException('down', new Request('GET', self::HEALTH_URL));
                }

                return $this->healthyResponse();
            }

            return new Response(200);
        });

        $logger = $this->makeLogger($httpClient);

        // Two failures — one short of the threshold.
        $logger->trackError('app-1', 'ERR_1', 'first');
        $logger->trackError('app-1', 'ERR_2', 'second');
        $this->assertSame(2, $this->getStaticValue('staticFailureCount'));

        // Service recovers; a successful send resets the breaker.
        $shouldFail = false;
        $logger->trackError('app-1', 'ERR_3', 'third');

        $this->assertSame(0, $this->getStaticValue('staticFailureCount'));
        $this->assertSame(0, $this->getStaticValue('staticOpenUntil'));
    }

    public function testCircuitBreakerStateIsSharedViaCacheWhenAvailable(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('request');

        $cache = $this->createMock(CacheInterface::class);

        // Another process already opened the circuit.
        $cache->method('get')
            ->with('signoz:cb:open_until', 0)
            ->willReturn(time() + 60);

        $logger = $this->makeLogger($httpClient, $cache);
        $logger->trackError('app-1', 'ERR_TEST', 'Something went wrong');
    }

    // -----------------------------------------------------------------
    // app.created flow (updated for the health-check gate)
    // -----------------------------------------------------------------

    public function testAppCreatedIsSentOnlyOncePerPublicKey(): void
    {
        $publicKey = getenv('PUBLIC_KEY') ?: 'FLWPUBK_TEST-0000000000000000000000000000000-X';
        $cacheKey = sprintf('signoz:app_created:%s', hash('sha256', $publicKey));

        $calls = [];
        $firstHttpClient = $this->mockHttpClient($calls, function (string $method, string $uri) use ($publicKey) {
            if ($uri === self::MERC_INFO_URL . $publicKey) {
                return new Response(200, [], json_encode(['mn' => 'Bajoski Software Developement']));
            }

            if ($uri === self::HEALTH_URL) {
                return $this->healthyResponse();
            }

            return new Response(200);
        });

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('has')->willReturnCallback(static function (string $key) use ($cacheKey): bool {
            // app_created flag not set yet; health result not cached.
            return false;
        });
        $cache->expects($this->atLeastOnce())
            ->method('set');

        $logger = new SignozServiceLogger($firstHttpClient, $publicKey, 'sandbox', $cache, '1.0.7');
        $logger->trackAppCreated($publicKey);

        // Expected sequence: merchant lookup -> health probe -> event POST.
        $this->assertCount(3, $calls);
        $this->assertSame(self::MERC_INFO_URL . $publicKey, $calls[0]['uri']);
        $this->assertSame(self::HEALTH_URL, $calls[1]['uri']);
        $this->assertSame(self::EVENTS_URL, $calls[2]['uri']);

        $this->assertSame('app.created', $calls[2]['options']['json']['name']);
        $this->assertSame($publicKey, $calls[2]['options']['json']['data']['public_key']);
        $this->assertSame('Bajoski-Software-Developement', $calls[2]['options']['json']['data']['app_id']);

        // Second logger: cache says app.created was already sent -> no HTTP at all.
        $this->resetStaticState();

        $secondHttpClient = $this->createMock(ClientInterface::class);
        $secondHttpClient->expects($this->never())->method('request');

        $secondCache = $this->createMock(CacheInterface::class);
        $secondCache->method('has')
            ->with($cacheKey)
            ->willReturn(true);
        $secondCache->expects($this->never())->method('set');

        $secondLogger = new SignozServiceLogger($secondHttpClient, $publicKey, 'sandbox', $secondCache, '1.0.7');
        $secondLogger->trackAppCreated($publicKey);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * Build a ClientInterface mock that records every call and delegates
     * the response to $handler. Replaces withConsecutive(), which was
     * removed in PHPUnit 10.
     *
     * @param array<int, array{method: string, uri: string, options: array}> $calls
     */
    private function mockHttpClient(array &$calls, callable $handler): ClientInterface
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('request')
            ->willReturnCallback(function (string $method, string $uri, array $options = []) use (&$calls, $handler) {
                $calls[] = ['method' => $method, 'uri' => $uri, 'options' => $options];

                return $handler($method, $uri, $options);
            });

        return $httpClient;
    }

    private function makeLogger(ClientInterface $httpClient, ?CacheInterface $cache = null): SignozServiceLogger
    {
        return new SignozServiceLogger(
            $httpClient,
            'FLWPUBK_TEST-0000000000000000000000000000000-X',
            'sandbox',
            $cache,
            '1.0.7'
        );
    }

    private function healthyResponse(): Response
    {
        return new Response(200, [], json_encode([
            'status'       => 'ok',
            'dependencies' => ['redis' => 'up'],
        ]));
    }

    private function serviceUnavailableException(): RequestException
    {
        return new RequestException(
            'Service Unavailable',
            new Request('POST', self::EVENTS_URL),
            new Response(503)
        );
    }

    private function resetStaticState(): void
    {
        $reflection = new ReflectionClass(SignozServiceLogger::class);

        $defaults = [
            'appCreatedSent'     => false,
            'staticFailureCount' => 0,
            'staticOpenUntil'    => 0,
            'staticHealthyUntil' => 0,
        ];

        foreach ($defaults as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue(null, $value);
        }
    }

    private function getStaticValue(string $name)
    {
        $reflection = new ReflectionClass(SignozServiceLogger::class);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue();
    }
}