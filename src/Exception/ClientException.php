<?php

namespace Flutterwave\Exception;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * ClientException
 */
class ClientException extends \RuntimeException implements ClientExceptionInterface
{
}
