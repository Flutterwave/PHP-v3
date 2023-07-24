<?php

namespace Flutterwave\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * RequestException
 */
class RequestException extends ClientException implements RequestExceptionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * Constructor of the class
     *
     * @param RequestInterface $request
     * @param string           $message
     * @param int              $code
     * @param Throwable|null   $previous
     */
    public function __construct(
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
