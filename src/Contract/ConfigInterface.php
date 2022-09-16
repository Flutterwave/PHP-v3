<?php

namespace Flutterwave\Contract;

use Psr\Log\LoggerInterface;
use Unirest\Request;

interface ConfigInterface
{
    public function getHttp(): Request;

    public static function getInstance(string $secretKey, string $publicKey, string $enc, string $env);

    public function getLoggerInstance(): LoggerInterface;

    public function getEncryptkey(): string;

    public function getPublicKey():string;

    public function getSecretKey():string;

    public function getEnv():string;

    public static function getDefaultTransactionPrefix():string;

}