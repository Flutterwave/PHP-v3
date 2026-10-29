<?php

declare(strict_types=1);

namespace Flutterwave;

use Flutterwave\Config\ForkConfig;
use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Exception\ApiException;
use Flutterwave\Helper\CheckCompatibility;
use Flutterwave\Monitoring\SignozServiceLogger;
use Flutterwave\Traits\PaymentFactory;
use Flutterwave\Traits\Setup\Configure;
use Flutterwave\Library\Modal;
use Psr\Http\Client\ClientExceptionInterface;

define('FLW_PHP_ASSET_DIR', __DIR__ . '../assets/');

/**
 * Flutterwave PHP SDK
 *
 * @author Flutterwave Developers <developers@flutterwavego.com>
 *
 * @version 1.0
 */
class Flutterwave extends AbstractPayment
{
    use Configure;
    use PaymentFactory;

    private SignozServiceLogger $signoz;
    private ?array $traceContext = null;

    /**
     * Flutterwave Construct
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->checkPageIsSecure();
        // create a log channel
        $this->logger = self::$config->getLoggerInstance();
        $this->createReferenceNumber();
        $this->logger->notice('Main Class Initializes....');
        
        if (!method_exists(self::$config, 'getSignoz')) {
            $this->signoz = self::getSignoz();
        } else {
            $this->signoz = self::$config->getSignoz();
        }

        $this->traceContext = $this->buildTraceContext();
        $this->signoz->setDefaultTraceContext($this->traceContext);
    }

    private function checkPageIsSecure()
    {
        if(!CheckCompatibility::isSsl() && 'production' === $this->getConfig()->getEnv()) {
            throw new \Exception('Flutterwave: cannot load checkout modal on an unsecure page - no SSL detected. ');
        }
    }

    private function buildTraceContext(?array $parentContext = null): array
    {
        $timestamp = gmdate('Y-m-d\TH:i:s.v\Z');
        $traceId = $parentContext['trace_id'] ?? $this->generateTraceId();
        $parentSpanId = $parentContext['span_id'] ?? null;

        return [
            'trace_id' => $traceId,
            'span_id' => $this->generateSpanId(),
            'parent_span_id' => $parentSpanId,
            'span_start_time' => $timestamp,
            'span_end_time' => $timestamp,
        ];
    }

    private function getTraceContextForEvent(): array
    {
        $nextContext = $this->buildTraceContext($this->traceContext);
        $this->traceContext = $nextContext;
        $this->signoz->setDefaultTraceContext($this->traceContext);

        return $nextContext;
    }

    private function generateTraceId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function generateSpanId(): string
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Sets the transaction amount
     *
     * @param string $amount Transaction amount
     * */
    public function setAmount(string $amount): object
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Sets the allowed payment methods
     *
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both
     */
    public function setPaymentOptions(string $paymentOptions): object
    {
        $this->paymentOptions = $paymentOptions;
        return $this;
    }

    /**
     * get event handler.
     *
     * @return EventHandlerInterface
     */
    public function getEventHandler()
    {
        return $this->handler;
    }

    /**
     * Sets the transaction description
     *
     * @param string $customDescription The description of the transaction
     */
    public function setDescription(string $customDescription): object
    {
        $this->customDescription = $customDescription;
        return $this;
    }

    /**
     * Sets the payment page logo
     *
     * @param string $customLogo Your Logo
     */
    public function setLogo(string $customLogo): object
    {
        $this->customLogo = $customLogo;
        return $this;
    }

    /**
     * Sets the payment page title
     *
     * @param string $customTitle A title for the payment.
     *                            It can be the product name, your business name or anything short and descriptive
     */
    public function setTitle(string $customTitle): object
    {
        $this->customTitle = $customTitle;
        return $this;
    }

    /**
     * Sets transaction country
     *
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     */
    public function setCountry(string $country): object
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Sets the transaction currency
     *
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
     */
    public function setCurrency(string $currency): object
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Sets the customer email
     *
     * @param string $customerEmail This is the paying customer's email
     */
    public function setEmail(string $customerEmail): object
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Sets the customer firstname
     *
     * @param string $customerFirstname This is the paying customer's firstname
     */
    public function setFirstname(string $customerFirstname): object
    {
        $this->customerFirstname = $customerFirstname;
        return $this;
    }

    /**
     * Sets the customer lastname
     *
     * @param string $customerLastname This is the paying customer's lastname
     */
    public function setLastname(string $customerLastname): object
    {
        $this->customerLastname = $customerLastname;
        return $this;
    }

    /**
     * Sets the customer phonenumber
     *
     * @param string $customerPhone This is the paying customer's phonenumber
     */
    public function setPhoneNumber(string $customerPhone): object
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * Sets the payment page button text
     *
     * @param string $payButtonText This is the text that should appear
     *                              on the payment button on the Rave payment gateway.
     */
    public function setPayButtonText(string $payButtonText): object
    {
        $this->payButtonText = $payButtonText;
        return $this;
    }

    /**
     * Sets the transaction redirect url
     *
     * @param string $redirectUrl This is where the Flutterwave will redirect to after
     *                            completing a payment
     */
    public function setRedirectUrl(string $redirectUrl): object
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     *
     * @param array $meta This are the other information you will like to store
     *                    with the transaction. It is a key => value array. eg. PNR for airlines,
     *                    product colour or attributes. Example. array('name' => 'femi')
     */
    public function setMetaData(array $meta): object
    {
        $this->meta = [$this->meta, $meta];
        return $this;
    }

    /**
     * Enforce the same trace context for request, transaction, and error events.
     */
    public function setTraceContext(array $traceContext): object
    {
        $this->traceContext = $this->buildTraceContext($traceContext);
        $this->signoz->setDefaultTraceContext($this->traceContext);
        return $this;
    }

    /**
     * Sets the event hooks for all available triggers
     *
     * @param EventHandlerInterface $handler This is a class that implements the
     *                                       Event Handler Interface
     */
    public function eventHandler(EventHandlerInterface $handler): object
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Requerys a previous transaction from the Rave payment gateway
     *
     * @param  string $referenceNumber This should be the reference number of the transaction you want to requery
     * @throws ClientExceptionInterface
     * @throws ApiException
     */
    public function requeryTransaction(string $referenceNumber): object
    {
        $this->txref = $referenceNumber;
        $this->requeryCount++;
        $this->logger->notice('Requerying Transaction....' . $this->txref);
        if (isset($this->handler)) {
            $this->handler->onRequery($this->txref);
        }

        $appId = $this->signoz->getAppId();
        $environment = $this->signoz->getCurrentEnvironment();
        $traceContext = $this->getTraceContextForEvent();

        $data = [
            'id' => (int) $referenceNumber,
            // 'only_successful' => '1'
        ];

        $url = '/transactions/' . $data['id'] . '/verify';

        $response = $this->getURL(static::$config, $url);

        //check the status is success.
        if ($response->status === 'success') {
            if ($response->data && $response->data->status === 'successful') {
                $this->logger->notice('Requeryed a successful transaction....' . json_encode($response->data));
                // Handle successful.
                if (isset($this->handler)) {
                    $final_tx_ref = $response->data->tx_ref;
                    $this->signoz->trackRequestSent($appId, $environment, 'GET', $final_tx_ref, $url, $traceContext);
                    if( 'production' === $environment ) {
                        $final_currency = $response->data->currency;
                        $final_amount = $response->data->amount;
                        $payment_type = $response->data->payment_type;
                        $final_fee = $response->data->app_fee;
                        $this->signoz->trackTransaction($appId,$final_tx_ref, $final_currency, (float) $final_amount, $payment_type, (float) $final_fee, $traceContext);
                    }
                    $this->handler->onSuccessful($response->data);
                }
            } elseif ($response->data && $response->data->status === 'failed') {
                // Handle Failure.
                $this->logger->warning('Requeryed a failed transaction....' . json_encode($response->data));
                if (isset($this->handler)) {
                    $this->handler->onFailure($response->data);
                }
            } else {
                // Handled an undecisive transaction. Probably timed out.
                $this->logger->warning(
                    'Requeryed an undecisive transaction....' . json_encode($response->data)
                );
                // I will requery again here. Just incase we have some devs that cannot setup a queue for requery.
                // I don't like this.
                if ($this->requeryCount > 4) {
                    // Now you have to setup a queue by force. We couldn't get a status in 5 requeries.
                    if (isset($this->handler)) {
                        $this->signoz->trackError($appId, 'TIMEOUT_ERROR', 'timedout while requerying transaction with id: ' . $referenceNumber, $traceContext);
                        $this->handler->onTimeout($this->txref, $response->data);
                    }
                } else {
                    $this->logger->notice('delaying next requery for 3 seconds');
                    sleep(3);
                    $this->logger->notice('Now retrying requery...');
                    $this->requeryTransaction($this->txref);
                }
            }
        } else {
            // Handle Requery Error.
            $this->signoz->trackError($appId, 'REQUERY_ERROR', 'Failed to requery transaction with id: ' . $referenceNumber, $traceContext);
            if (isset($this->handler)) {
                $this->handler->onRequeryError($response->data);
            }
        }
        return $this;
    }

    /**
     * @deprecated Use render('inline')->getHtml() instead.
     * Will be removed in a future version.
     */
    public function initialize(): void
    {
        $this->traceContext = $this->buildTraceContext($this->traceContext);
        $this->signoz->setDefaultTraceContext($this->traceContext);

        @trigger_error(
            'initialize() is deprecated and will be removed in a future version. Use render(\'inline\')->with([...])->getHtml() instead.',
            E_USER_DEPRECATED
        );
        
        $this->createCheckSum();

        $checkoutConfig = [
            'public_key'        => self::$config->getPublicKey(),
            'tx_ref'            => $this->txref,
            'amount'            => (float) $this->amount,
            'currency'          => $this->currency,
            'country'           => $this->country,
            'redirect_url'      => $this->redirectUrl,
            'payment_method'    => $this->paymentOptions,
            'email'             => $this->customerEmail,
            'phone_number'      => $this->customerPhone,
            'first_name'        => $this->customerFirstname,
            'last_name'         => $this->customerLastname,
            'customizations'    => [
                'title'         => $this->customTitle,
                'description'   => $this->customDescription,
                'logo'          => $this->customLogo,
            ],
        ];

        echo $this->render(Modal::POPUP)->with($checkoutConfig)->getHtml();
    }

    /**
     * Handle canceled payments with this method
     *
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     */
    public function paymentCanceled(string $referenceNumber): object
    {
        $this->logger->notice('Payment was canceled by user..' . $referenceNumber);
        if (isset($this->handler)) {
            $this->handler->onCancel($referenceNumber);
        }
        return $this;
    }

    public static function setUp(array $config): void
    {
        self::$config = ForkConfig::setUp(
            $config['secret_key'],
            $config['public_key'],
            $config['encryption_key'],
            $config['environment']
        );
    }

    public function render(string $modalType): Modal
    {
        $data = [
            'tx_ref' => $this->txref,
        ];
        return new Modal($modalType, $data, $this->getEventHandler(), self::$config);
    }
}
