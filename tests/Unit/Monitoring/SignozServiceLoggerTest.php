<?php

declare(strict_types=1);

namespace Flutterwave\Test\Unit\Monitoring;

use Flutterwave\Monitoring\SignozServiceLogger;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;
use ReflectionProperty;

class SignozServiceLoggerTest extends TestCase
{
    private const PUBLIC_KEY = getEnv('FW_PHP_PUBLIC_KEY', 'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X');

    protected function tearDown(): void
    {
        $this->resetAppCreatedFlag();
    }

    // public function testAppCreatedIsSentOnlyOncePerPublicKey(): void
    // {
    //     $firstHttpClient = $this->createMock(ClientInterface::class);
    //     $secondHttpClient = $this->createMock(ClientInterface::class);
    //     $cache = $this->createMock(CacheInterface::class);

    //     $cacheKey = sprintf('signoz:app_created:%s', hash('sha256', self::PUBLIC_KEY));

    //     $cache->expects($this->exactly(2))
    //         ->method('has')
    //         ->with($cacheKey)
    //         ->willReturnOnConsecutiveCalls(false, true);

    //     $cache->expects($this->once())
    //         ->method('set')
    //         ->with($cacheKey, true);

    //     $firstHttpClient->expects($this->exactly(2))
    //         ->method('request')
    //         ->withConsecutive(
    //             [
    //                 'GET',
    //                 'https://api.ravepay.co/flwv3-pug/getpaidx/api/mercinfo?PBFPubKey=' . self::PUBLIC_KEY,
    //                 $this->callback(static function (array $options): bool {
    //                     return isset($options['headers']['Content-Type']) && $options['headers']['Content-Type'] === 'application/json';
    //                 }),
    //             ],
    //             [
    //                 'POST',
    //                 'https://signozservice-prod.f4b-flutterwave.com/events',
    //                 $this->callback(static function (array $options): bool {
    //                     if (!isset($options['json']['name'], $options['json']['data']['public_key'])) {
    //                         return false;
    //                     }

    //                     return $options['json']['name'] === 'app.created'
    //                         && $options['json']['data']['public_key'] === self::PUBLIC_KEY;
    //                 }),
    //             ]
    //         )
    //         ->willReturnOnConsecutiveCalls(
    //             new Response(200, [], json_encode(['mn' => 'Bajoski Software Developement'])),
    //             new Response(200)
    //         );

    //     $logger = new SignozServiceLogger($firstHttpClient, self::PUBLIC_KEY, 'sandbox', $cache, '1.0.7');
    //     $logger->trackAppCreated(self::PUBLIC_KEY);

    //     $this->resetAppCreatedFlag();

    //     $secondHttpClient->expects($this->never())
    //         ->method('request')
    //         ->with($this->anything(), $this->anything(), $this->anything());

    //     $cache->expects($this->once())
    //         ->method('has')
    //         ->with($cacheKey)
    //         ->willReturn(true);

    //     $cache->expects($this->never())
    //         ->method('set');

    //     $secondLogger = new SignozServiceLogger($secondHttpClient, self::PUBLIC_KEY, 'sandbox', $cache, '1.0.7');
    //     $secondLogger->trackAppCreated(self::PUBLIC_KEY);
    // }

    private function resetAppCreatedFlag(): void
    {
        $reflection = new ReflectionClass(SignozServiceLogger::class);
        $property = $reflection->getProperty('appCreatedSent');
        $property->setAccessible(true);
        $property->setValue(false);
    }
}
