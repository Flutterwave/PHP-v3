<?php

declare(strict_types=1);

namespace Flutterwave;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Traits\ApiOperations as Api;
use Flutterwave\Traits\PayloadOperations as Payload;
use Psr\Log\LoggerInterface;

abstract class AbstractPayment
{
    use Api\Post, Api\Get, Payload\Prepare;

    public string $secretKey;
    public string $txref;
    public $type;
    public LoggerInterface $logger;
//    protected ?string $integrityHash = null;
    protected string $payButtonText = 'Proceed with Payment';
    protected string $redirectUrl;
    protected array $meta = [];
    //protected $env;
    protected string $transactionPrefix;
    // public $logger;
    protected EventHandlerInterface $handler;
    protected string $baseUrl;
    protected string $transactionData;
    protected bool $overrideTransactionReference;
    protected int $requeryCount = 0;
    protected static ?array $methods = null;

    //Payment information
    protected $account;
    protected $key;
    protected $pin;
    protected $options;
    protected string $amount;
    protected $paymentOptions = null;
    protected $customDescription;
    protected $customLogo;
    protected $customTitle;
    protected $country;
    protected $currency;
    protected $customerEmail;
    protected $customerFirstname;
    protected $customerLastname;
    protected $customerPhone;

    //EndPoints
    protected string $end_point;
    protected string $flwRef;
    protected static ConfigInterface $config;

    public function __construct(string $prefix, bool $overrideRefWithPrefix)
    {
        $this->transactionPrefix = $overrideRefWithPrefix ? $prefix : self::$config::DEFAULT_PREFIX . '_';
        $this->baseUrl = self::$config::BASE_URL;
    }

    abstract public function initialize(): void;
}
