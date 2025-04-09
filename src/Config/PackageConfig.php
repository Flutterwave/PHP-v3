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

final class PackageConfig extends AbstractConfig implements ConfigInterface
{
    private function __construct(string $secretKey, string $publicKey, string $encryptKey, string $env)
    {
        parent::__construct($secretKey, $publicKey, $encryptKey, $env);
    }

    public static function setUp(string $secretKey, string $publicKey, string $enc, string $env, ?LoggerInterface $customLogger = null): ConfigInterface
    {
        if (is_null(self::$instance)) {
            $instance = new self($secretKey, $publicKey, $enc, $env);
            if ($customLogger) {
                $instance->logger = $customLogger;
            } else {
                $vendorPath = dirname(__DIR__, 4);
                $rootPath = dirname($vendorPath);

                $logSubDir = $_ENV['FLW_LOG_DIR'] ?? 'logs';
                $logDir = $rootPath . DIRECTORY_SEPARATOR . $logSubDir;

                if (!is_dir($logDir)) {
                    if (!mkdir($logDir, 0775, true) && !is_dir($logDir)) {
                        throw new \RuntimeException("Flutterwave: Failed to create log directory at $logDir");
                    }
                }

                if (!is_writable($logDir)) {
                    throw new \RuntimeException("Flutterwave: Log directory is not writable: $logDir");
                }

                $instance->logger->pushHandler(
                    new RotatingFileHandler($logDir . '/' . self::LOG_FILE_NAME, 90)
                );
            }
            self::$instance = $instance;
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
