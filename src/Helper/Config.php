<?php

declare(strict_types=1);

namespace Flutterwave\Helper;

use Flutterwave\Contract\ConfigInterface;
use GuzzleHttp\Client;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

use function is_null;

/**
 * Class Payload.
 *
 * @deprecated use \Flutterwave\Config\PackageConfig instead
 */
class Config implements ConfigInterface
{
    public const PUBLIC_KEY = 'PUBLIC_KEY';
    public const SECRET_KEY = 'SECRET_KEY';
    public const ENCRYPTION_KEY = 'ENCRYPTION_KEY';
    public const ENV = 'ENV';
    public const DEFAULT_PREFIX = 'FW|PHP';
    public const LOG_FILE_NAME = 'flutterwave-php.log';
    protected Logger $logger;
    private string $secret;
    private string $public;

    private static ?Config $instance = null;
    private string $env;
    private ClientInterface $http;
    private string $enc;

    private function __construct(string $secretKey, string $publicKey, string $encryptKey, string $env)
    {
        $this->secret = $secretKey;
        $this->public = $publicKey;
        $this->enc = $encryptKey;
        $this->env = $env;

        // when creating a custom config, you may choose to use other dependencies here.
        // http-client - Guzzle, logger - Monolog.
        $this->http = new Client(['base_uri' => EnvVariables::BASE_URL, 'timeout' => 60 ]);
        $log = new Logger('Flutterwave/PHP');
        $this->logger = $log;
        $log->pushHandler(new RotatingFileHandler(__DIR__ . "../../../../../../" . self::LOG_FILE_NAME, 90));
    }

    public static function setUp(string $secretKey, string $publicKey, string $enc, string $env): ConfigInterface
    {
        if (is_null(self::$instance)) {
            return new Config($secretKey, $publicKey, $enc, $env);
        }
        return self::$instance;
    }

    public function getHttp(): ClientInterface
    {
        return $this->http ?? new Client();
    }

    public function getLoggerInstance(): LoggerInterface
    {
        return $this->logger;
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

    public static function getDefaultTransactionPrefix(): string
    {
        return self::DEFAULT_PREFIX;
    }
}
