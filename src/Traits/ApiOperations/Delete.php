<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;

trait Delete
{
    public function deleteURL(ConfigInterface $config, string $url): string
    {
        $response = (new Http($config))->request(null, 'DELETE', $url);

        return '';
    }
}
