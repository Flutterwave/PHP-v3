<?php

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Request;
use Unirest\Request\Body;

trait Put
{

    function putURL($data)
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::put($url, $headers, $body);
        return $response->raw_body;
    }

}