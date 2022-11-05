<?php

namespace Flutterwave\Traits\ApiOperations;

use Unirest\Request;

trait Delete
{
    /**
     * @param string $url
     * @return string
     */
    function delURL(string $url): string
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        //$body = Body::json($data);
        $path = $this->baseUrl . '/' . $this->end_point;
        $response = Request::delete($path . $url, $headers);
        return $response->raw_body;
    }
}