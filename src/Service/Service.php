<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\ServiceInterface;
use Flutterwave\Helper\Config;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use function is_null;
use Psr\Log\LoggerInterface;
use stdClass;

class Service implements ServiceInterface
{
    public const ENDPOINT = '';
    public ?Payload $payload;
    public ?Customer $customer;
    protected string $baseUrl;
    protected LoggerInterface $logger;
    protected ConfigInterface $config;
    protected string $url;
    protected string $secret;
    private static string $name = 'service';
    private static ?ConfigInterface $spareConfig = null;
    private ClientInterface $http;

    public function __construct(?ConfigInterface $config = null)
    {
        self::bootstrap($config);
        $this->customer = new Customer();
        $this->payload = new Payload();
        $this->config = is_null($config) ? self::$spareConfig : $config;
        $this->http = $this->config->getHttp();
        $this->logger = $this->config->getLoggerInstance();
        $this->secret = $this->config->getSecretKey();
        $this->url = $this->config::getBaseUrl().'/';
        $this->baseUrl = $this->config::getBaseUrl();
    }

    public function getName(): string
    {
        return self::$name;
    }

    /**
     * @param array|null $data
     * @param string $verb
     * @param string $additionalurl
     * @return stdClass
     * @throws GuzzleException
     */
    protected function request(?array $data = null, string $verb = 'GET', string $additionalurl = ''): stdClass
    {
        $secret = $this->config->getSecretKey();

        switch ($verb) {
            case 'POST':
                $response = $this->http->request('POST', $this->url.$additionalurl, [
                    'debug' => false, # TODO: turn to false  on release.
                    'headers' => [
                        'Authorization' => "Bearer $secret",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data,
                ]);
                break;
            case 'PUT':
                $response = $this->http->request('PUT', $this->url.$additionalurl, [
                    'debug' => false, # TODO: turn to false  on release.
                    'headers' => [
                        'Authorization' => "Bearer $secret",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data ?? [],
                ]);
                break;
            case 'DELETE':
                $response = $this->http->request('DELETE', $this->url.$additionalurl, [
                    'debug' => false,
                    'headers' => [
                        'Authorization' => "Bearer $secret",
                        'Content-Type' => 'application/json',
                    ],
                ]);
                break;
            default:
                $response = $this->http->request('GET', $this->url.$additionalurl, [
                    'debug' => false,
                    'headers' => [
                        'Authorization' => "Bearer $secret",
                        'Content-Type' => 'application/json',
                    ],
                ]);
                break;
        }

        $body = $response->getBody()->getContents();
        return json_decode($body);
    }

    protected function checkTransactionId($transactionId): void
    {
        $pattern = '/([0-9]){7}/';
        $is_valid = preg_match_all($pattern, $transactionId);

        if (! $is_valid) {
            $this->logger->warning('Transaction Service::cannot verify invalid transaction id. ');
            throw new InvalidArgumentException('cannot verify invalid transaction id.');
        }
    }

    private static function bootstrap(?ConfigInterface $config = null): void
    {
        if (is_null($config)) {
            require __DIR__.'/../../setup.php';
            $config = Config::setUp(
                $_SERVER[Config::SECRET_KEY],
                $_SERVER[Config::PUBLIC_KEY],
                $_SERVER[Config::ENCRYPTION_KEY],
                $_SERVER['ENV']
            );
        }
        self::$spareConfig = $config;
    }
}
