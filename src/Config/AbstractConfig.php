<?php

declare(strict_types=1);

namespace Flutterwave\Config;

use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Flutterwave;
use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Monitoring\SignozServiceLogger;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Flutterwave\Helper\EnvVariables;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

abstract class AbstractConfig
{
    public const PUBLIC_KEY = 'PUBLIC_KEY';
    public const SECRET_KEY = 'SECRET_KEY';
    public const ENCRYPTION_KEY = 'ENCRYPTION_KEY';
    public const ENV = 'ENV';
    public const DEFAULT_PREFIX = 'FW_PHP';
    public const LOG_FILE_NAME = 'flutterwave-php.log';
    public Logger $logger;
    protected string $secret;
    protected string $public;
    public SignozServiceLogger $signoz;

    protected static ?ConfigInterface $instance = null;
    protected string $env;
    private ClientInterface $http;
    protected string $enc;

    protected function __construct(string $secret_key, string $public_key, string $encrypt_key, string $env)
    {
        $this->secret = $secret_key;
        $this->public = $public_key;
        $this->enc = $encrypt_key;
        $this->env = $env;

        $this->http = new Client(
            [
            'base_uri' => EnvVariables::BASE_URL,
            'timeout' => 60,
            'headers' => ['User-Agent' => sprintf(
                'FlutterwavePHP/%d', EnvVariables::SDK_VERSION
            )],
            RequestOptions::VERIFY => 
            \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
            ]
        );

        $log = new Logger('Flutterwave/PHP');
        $this->logger = $log;
        $cache = new Psr16Cache(new FilesystemAdapter('flutterwave_signoz'));
        $this->signoz = new SignozServiceLogger($this->http, $this->getPublicKey(), $this->getEnv(), $cache, EnvVariables::SDK_VERSION);
        // Track app initialization once per lifecycle
        $this->signoz->trackAppCreated($this->getPublicKey());
    }

    abstract public static function setUp(
        string $secretKey,
        string $publicKey,
        string $enc,
        string $env
    ): ConfigInterface;

    public function getHttp(): ClientInterface
    {
        return $this->http;
    }

    public function getLoggerInstance(): LoggerInterface
    {
        return $this->logger;
    }

    abstract public function getEncryptkey(): string;

    abstract public function getPublicKey(): string;

    abstract public function getSecretKey(): string;

    abstract public function getEnv(): string;

    public static function getDefaultTransactionPrefix(): string
    {
        return self::DEFAULT_PREFIX;
    }

    public function getSignoz(): SignozServiceLogger
    {
        return $this->signoz;
    }
}
