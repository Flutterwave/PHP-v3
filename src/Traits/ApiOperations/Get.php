<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Request;

trait Get
{
    public function getURL(string $url): string
    {
        // make request to endpoint using unirest.
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = ['Content-Type' => 'application/json', 'Authorization' => $bearerTkn];
        //$body = Body::json($data);
        $path = $this->baseUrl . '/' . $this->end_point;
        $response = Request::get($path . $url, $headers);
        return $response->raw_body;    // Unparsed body
    }
}
