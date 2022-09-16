<?php

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Request;
use Unirest\Request\Body;

trait Post
{
    function postURL($data): string
    {
        // make request to endpoint using unirest
        $bearerTkn = 'Bearer ' . $this->config->getSecretKey();
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::post($url, $headers, $body);
        return $response->raw_body;    // Unparsed body
    }
}