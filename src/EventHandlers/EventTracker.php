<?php

namespace Flutterwave\EventHandlers;

use Unirest\Request;
use Unirest\Request\Body;

trait EventTracker
{

    static $time_start = 0;
    static $response_time = 0;

    static function startRecording()
    {
        self::$time_start = microtime(true);
    }

    static function setResponseTime()
    {
        self::$response_time = microtime(true) - self::$time_start;
    }

    static function sendAnalytics($title)
    {
        if (self::$response_time <= 0)
            self::setResponseTime();

        $url = "https://kgelfdz7mf.execute-api.us-east-1.amazonaws.com/staging/sendevent";

        $data = [
            "publicKey" => getenv('PUBLIC_KEY'),
            "language" => "PHP V3",
            "version" => "1.0.0",
            "title" => $title,
            "message" => self::$response_time
        ];
        $body = Body::json($data);
        Request::post($url, [], $body);

        self::resetTime();
    }

    private static function resetTime() {
        self::$time_start = 0;
        self::$response_time = 0;
    }
}
