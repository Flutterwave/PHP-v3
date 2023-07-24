<?php

/**
 * Handle Configuration for Composer Installation.
 */

declare(strict_types=1);

namespace Flutterwave\Config;

use Flutterwave\Contract\ConfigInterface;
use GuzzleHttp\Client;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

use function is_null;

final class ForkConfig extends AbstractConfig implements ConfigInterface
{
    private function __construct(string $secretKey, string $publicKey, string $encryptKey, string $env)
    {
        parent::__construct($secretKey, $publicKey, $encryptKey, $env);
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__ . "/../../" . self::LOG_FILE_NAME, 90));
    }

    public static function setUp(string $secretKey, string $publicKey, string $enc, string $env): ConfigInterface
    {
        if (is_null(self::$instance)) {
            return new self($secretKey, $publicKey, $enc, $env);
        }
        return self::$instance;
    }

    public function getEncryptkey(): string
    {
        return $this->enc;
    }

    public function getPublicKey(): string
    {
        return $this->public;
    }

    public function getSecretKey(): string
    {
        return $this->secret;
    }

    public function getEnv(): string
    {
        return $this->env;
    }
}
