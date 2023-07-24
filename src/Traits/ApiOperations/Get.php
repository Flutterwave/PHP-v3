<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Exception\ApiException;
use Flutterwave\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

trait Get
{
    /**
     * @param  ConfigInterface $config
     * @param  string          $url
     * @return stdClass
     * @throws ClientExceptionInterface
     * @throws ApiException
     */
    public function getURL(ConfigInterface $config, string $url): stdClass
    {
        $response = (new Http($config))->request(null, 'GET', $url);
        if ($response->status === 'success') {
            return $response;
        }
        throw new ApiException($response->message);
    }
}
