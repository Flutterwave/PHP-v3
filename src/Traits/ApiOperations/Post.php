<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Exception;
use Unirest\Request;
use Unirest\Request\Body;

trait Post
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public function postURL(array $data): string
    {
        // make request to endpoint using unirest
        $bearerTkn = 'Bearer ' . $this->config->getSecretKey();
        $headers = ['Content-Type' => 'application/json', 'Authorization' => $bearerTkn];
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::post($url, $headers, $body);
        return $response->raw_body;    // Unparsed body
    }
}
