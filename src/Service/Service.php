<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\ServiceInterface;
use Flutterwave\Helper\Config;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use stdClass;
use function is_null;

class Service implements ServiceInterface
{
    const ENDPOINT = "";
    private static string $name = "service";
    /**
     * @var ConfigInterface|Config|null
     */
    private static $spareConfig;
    protected string $baseUrl;
    protected LoggerInterface $logger;
    private ClientInterface $http;
    protected ConfigInterface $config;
    public ?Payload $payload;
    public ?Customer $customer;
    protected string $url;
    protected string $secret;

    public function __construct(?ConfigInterface $config = null)
    {
        self::bootstrap($config);
        $this->customer = new Customer;
        $this->payload = new Payload;
        $this->config = (is_null($config))?self::$spareConfig:$config;
        $this->http = $this->config->getHttp();
        $this->logger = $this->config->getLoggerInstance();
        $this->secret = $this->config->getSecretKey();
        $this->url  = $this->config::getBaseUrl()."/";
        $this->baseUrl = $this->config::getBaseUrl();
    }

    /**
     * @param array|null $data
     * @param string $verb
     * @param string $additionalurl
     * @return stdClass
     * @throws GuzzleException
     */
    protected function request(?array $data = null, string $verb = 'GET', string $additionalurl = ""): stdClass
    {
        $secret = $this->config->getSecretKey();

        switch ($verb){
            case 'POST':
                $response = $this->http->request("POST", $this->url.$additionalurl,[
                    'debug' => FALSE, # TODO: turn to false  on release.
                    'headers' => [
                        "Authorization" => "Bearer $secret",
                        "Content-Type" => "application/json",
                    ],
                    "json" =>  $data
                ]);
                break;
            case 'PUT':
                $response = $this->http->request("PUT", $this->url.$additionalurl,[
                    'debug' => FALSE, # TODO: turn to false  on release.
                    'headers' => [
                        "Authorization" => "Bearer $secret",
                        "Content-Type" => "application/json",
                    ],
                    'json' =>  $data ?? []
                ]);
                break;
            case 'DELETE':
                $response = $this->http->request( "DELETE", $this->url.$additionalurl,[
                    'debug' => FALSE,
                    'headers' => [
                        "Authorization" => "Bearer $secret",
                        "Content-Type" => "application/json"
                    ]
                ]);
                break;
            default:
                $response = $this->http->request( "GET",$this->url.$additionalurl,[
                    'debug' => FALSE,
                    'headers' => [
                        "Authorization" => "Bearer $secret",
                        "Content-Type" => "application/json"
                    ]
                ]);
                break;
        }

        $body = $response->getBody();

        return json_decode($body);
    }

    public function getName(): string
    {
        return self::$name;
    }

    protected function checkTransactionId($transactionId):void
    {
        $pattern = '/([0-9]){7}/';
        $is_valid = preg_match_all($pattern,$transactionId);

        if(!$is_valid){
            $this->logger->warning("Transaction Service::cannot verify invalid transaction id. ");
            throw new InvalidArgumentException("cannot verify invalid transaction id.");
        }
    }

    private  static function bootstrap(?ConfigInterface $config = null)
    {
        if(is_null($config))
        {
            require __DIR__."/../../setup.php";
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