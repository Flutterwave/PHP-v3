<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\FactoryInterface;
use Flutterwave\Contract\ServiceInterface;
use Flutterwave\Config\ForkConfig;
use Flutterwave\Factories\CustomerFactory as Customer;
use Flutterwave\Factories\PayloadFactory as Payload;
use Flutterwave\Helper\Config;
use Flutterwave\Helper\EnvVariables;
use Psr\Http\Client\ClientInterface;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use stdClass;

use function is_null;

class Service implements ServiceInterface
{
    public const ENDPOINT = '';
    public ?FactoryInterface $payload;
    public ?FactoryInterface $customer;
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
        $this->url = EnvVariables::BASE_URL . '/';
        $this->baseUrl = EnvVariables::BASE_URL;
    }

    public function getName(): string
    {
        return self::$name;
    }

    /**
     * @param  array|null $data
     * @param  string     $verb
     * @param  string     $additionalurl
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function request(
        ?array $data = null,
        string $verb = 'GET',
        string $additionalurl = '',
        bool $overrideUrl = false
    ): stdClass {

        $secret = $this->config->getSecretKey();
        $url = $this->getUrl($overrideUrl, $additionalurl);

        switch ($verb) {
        case 'POST':
            $response = $this->http->request(
                'POST', $url, [
                'debug' => false, // TODO: turn to false  on release.
                'headers' => [
                    'Authorization' => "Bearer $secret",
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
                    ]
            );
            break;
        case 'PUT':
            $response = $this->http->request(
                'PUT', $url, [
                'debug' => false, // TODO: turn to false  on release.
                'headers' => [
                    'Authorization' => "Bearer $secret",
                    'Content-Type' => 'application/json',
                ],
                'json' => $data ?? [],
                    ]
            );
            break;
        case 'DELETE':
            $response = $this->http->request(
                'DELETE', $url, [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer $secret",
                    'Content-Type' => 'application/json',
                ],
                    ]
            );
            break;
        default:
            $response = $this->http->request(
                'GET', $url, [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer $secret",
                    'Content-Type' => 'application/json',
                ],
                    ]
            );
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
            include __DIR__ . '/../../setup.php';

            if ('composer' === $flutterwave_installation) {
                $config = Config::setUp(
                    $keys[Config::SECRET_KEY],
                    $keys[Config::PUBLIC_KEY],
                    $keys[Config::ENCRYPTION_KEY],
                    $keys[Config::ENV]
                );
            }

            if ('manual' === $flutterwave_installation) {
                $config = ForkConfig::setUp(
                    $keys[Config::SECRET_KEY],
                    $keys[Config::PUBLIC_KEY],
                    $keys[Config::ENCRYPTION_KEY],
                    $keys[Config::ENV]
                );
            }
        }
        self::$spareConfig = $config;
    }
    private function getUrl(bool $overrideUrl, string $additionalurl): string
    {
        if ($overrideUrl) {
            return $additionalurl;
        }

        return $this->url . $additionalurl;
    }
}
