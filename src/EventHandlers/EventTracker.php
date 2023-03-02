<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

use Flutterwave\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;

trait EventTracker
{
    public static float $time_start = 0;
    public static float $response_time = 0;

    public static function startRecording(): void
    {
        self::$time_start = microtime(true);
    }

    public static function setResponseTime(): void
    {
        self::$response_time = microtime(true) - self::$time_start;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public static function sendAnalytics($title): void
    {
        if (self::$response_time <= 0) {
            self::setResponseTime();
        }

        $url = 'https://kgelfdz7mf.execute-api.us-east-1.amazonaws.com/staging/sendevent';

        $data = [
            'publicKey' => getenv('PUBLIC_KEY'),
            'language' => 'PHP V3',
            'version' => '1.0.0',
            'title' => $title,
            'message' => self::$response_time,
        ];

        $response = (new Http(static::$config))->request($data, 'POST', $url, true);

        self::resetTime();
    }

    private static function resetTime(): void
    {
        self::$time_start = 0;
        self::$response_time = 0;
    }
}
