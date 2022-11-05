<?php

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Exception;
use Unirest\Request;
use Unirest\Request\Body;

trait Put
{
    /**
     * @param mixed[] $data
     * @return string
     * @throws Exception
     */
    function putURL(array $data): string
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::put($url, $headers, $body);
        return $response->raw_body;
    }

}