<?php

declare(strict_types=1);

namespace Flutterwave\Traits\ApiOperations;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Service\Service as Http;
use Psr\Http\Client\ClientExceptionInterface;

trait Post
{
    /**
     * @param ConfigInterface $config
     * @param array           $data
     *
     * @return string
     * @throws ClientExceptionInterface
     */
    public function postURL(ConfigInterface $config, array $data): string
    {
        $response = (new Http($config))->request($data, 'POST', $this->end_point);

        return '';
    }
}
