<?php

/**
 * Payment Modal.
 */

declare(strict_types=1);

namespace Flutterwave\Library;

use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Helper\CheckoutHelper;
use Flutterwave\Service\Service as Http;
use Flutterwave\Entities\Payload;
use Psr\Log\LoggerInterface;

final class Modal
{
    public const POPUP = 'inline';
    public const STANDARD = 'standard';
    private \Flutterwave\Entities\Payload $payload;
    private \Flutterwave\Entities\Customer $customer;
    private string $type;
    private EventHandlerInterface $paymentHandler;
    private array $generatedTransactionData;

    private static object $config;
    private LoggerInterface $logger;

    private array $functions = [
        'with' => 'with',
        'getHtml' => 'getHtml',
        'getUrl' => 'getUrl'
    ];

    public function __construct(
        string $type,
        array $generatedTransactionData,
        EventHandlerInterface $paymentHandler,
        $config
    ) {
        if ($type !== self::POPUP && $type !== self::STANDARD) {
            $type = self::STANDARD;
        }

        $this->type = $type;
        $this->generatedTransactionData = $generatedTransactionData;
        $this->paymentHandler = $paymentHandler;
        self::$config = $config;
        $this->logger = self::$config->getLoggerInstance();
    }

    public function with(array $args)
    {

        $args['customer'] = [
            'email' => $args['email'] ?? '',
            'name' => $args['first_name'] . " " . $args['last_name'],
        ];

        if (isset($args['customer']['name'])) {
            $args['customer']['full_name'] = $args['customer']['name'];
        }

        if (isset($args['phone_number'])) {
            $args['customer']['phone'] = $args['phone_number'];
        } else {
            $args['customer']['phone'] = '';
        }

        $this->customer = (new \Flutterwave\Factories\CustomerFactory())->create($args['customer']);

        $args['customer'] = $this->customer;

        if (isset($args['tx_ref'])) {
            $this->logger->notice(
                'Changing transaction reference from ' .
                $this->generatedTransactionData['tx_ref'] . ' to ' . $args['tx_ref']
            );

            $args = array_merge($this->generatedTransactionData, $args);
        } else {
            $args = array_merge($args, $this->generatedTransactionData);
        }
        $this->payload = (new \Flutterwave\Factories\PayloadFactory())->create($args);

        $this->payload->set('redirect_url', $args['redirect_url']);
        $this->payload->set('payment_method', $args['payment_method']);

        $dataToHash = [
            'amount' => $args['amount'],
            'currency' => $args['currency'],
            'email' => $args['email'],
            'tx_ref' => $args['tx_ref']
        ];

        $secretKey = self::$config->getSecretKey();

        $this->payload->set('payload_hash', CheckoutHelper::generateHash($dataToHash, $secretKey));

        return $this;
    }

    public function getHtml()
    {
        if ($this->type !== self::POPUP) {
            return $this->returnUrl();
        }

        $default_options = CheckoutHelper::getDefaultPaymentOptions();

        $payload = $this->payload->toArray('modal');
        $currency = $payload['currency'];
        $country = CheckoutHelper::getSupportedCountry($currency);
        $payment_method = $payload['payment_method'] ?? $default_options;

        $this->logger->info('Rendering Payment Modal..');
        $html = '';

        $html .= '<html lang="en">';
        $html .= '<body>';
        $html .= '<div style="display: flex; flex-direction: row;justify-content: center; align-content: center ">
        Proccessing...<img src="../assets/images/ajax-loader.gif"  alt="loading-gif"/></div>';
        $html .= '<script type="text/javascript" src="https://checkout.flutterwave.com/v3.js"></script>';
        $html .= '<script>';
        $html .= 'document.addEventListener("DOMContentLoaded", function(event) {';
        $html .= 'FlutterwaveCheckout({
            public_key: "' . self::$config->getPublicKey() . '",
            tx_ref: "' . $payload['tx_ref'] . '",
            amount: ' . $payload['amount'] . ',
            currency: "' . $currency . '",
            country: "' . $country . '",
            payment_options: "' . $payment_method . '",
            redirect_url:"' . $payload['redirect_url'] . '",
            payload_hash:"' . $payload['payload_hash'] . '",
            customer: {
              email: "' . $payload['email'] . '",
              phone_number: "' . $payload['phone_number'] . '",
              name: "' . $payload['fullname'] . '",
            },
            callback: function (data) {
              console.log(data);
            },
            onclose: function() {
                window.location = "?status=cancelled&tx_ref=' . $payload['tx_ref'] . '";
            }
        });';
        $html .= '});';
        $html .= '</script>';
        $html .= '</body>';
        $html .= '</html>';

        $this->logger->info('Rendered Payment Modal Successfully..');
        return $html;
    }

    public function getUrl()
    {

        if ($this->type !== self::STANDARD) {
            return $this->returnHtml();
        }

        $default_options = CheckoutHelper::getDefaultPaymentOptions();
        $payload         = $this->payload->toArray('modal');
        $currency        = $payload['currency'];
        $country         = CheckoutHelper::getSupportedCountry($currency);
        
        $payload['country'] = $country;
        $payload['customer'] = $payload['customer']->toArray();
        $payload['payment_method'] ?? $default_options;

        $this->logger->info('Generating Payment link for [' . $payload['tx_ref'] . ']');
        $response = (new Http(self::$config))->request($payload, 'POST', 'payments');
        return $response->data->link;
    }
}
