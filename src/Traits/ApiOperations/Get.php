<?php

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Request;

trait Get
{
    /**
     * makes a get call to the api
     * */

    function getURL($url)
    {
        // make request to endpoint using unirest.
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        //$body = Body::json($data);
        $path = $this->baseUrl . '/' . $this->end_point;
        $response = Request::get($path . $url, $headers);
        return $response->raw_body;    // Unparsed body
    }
}