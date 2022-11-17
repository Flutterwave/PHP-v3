<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Exception;
use Unirest\Request;
use Unirest\Request\Body;

trait Put
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public function putURL(array $data): string
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = ['Content-Type' => 'application/json', 'Authorization' => $bearerTkn];
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::put($url, $headers, $body);
        return $response->raw_body;
    }
}
