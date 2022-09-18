<?php
namespace Flutterwave;

//require __DIR__ . '/../setup.php'; // Uncomment this autoloader if you need it

use Flutterwave\EventHandlers\EventHandlerInterface;
use Flutterwave\Traits\PaymentFactory;
use Unirest\Request;
use Unirest\Request\Body;
use Flutterwave\Traits\Setup\Configure;

define("FLW_PHP_ASSET_DIR", __DIR__."../assets/");

/**
 * Flutterwave PHP SDK
 * @author Flutterwave Developers <developers@flutterwavego.com>
 * @version 1.0
 **/
class Flutterwave extends AbstractPayment
{
    use Configure,PaymentFactory;
    /**
     * Construct
     * @param boolean $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     * @return object
     * */
    function __construct(string $prefix, bool $overrideRefWithPrefix = false)
    {
        parent::__construct();

        $this->transactionPrefix = $overrideRefWithPrefix ? $prefix : self::$config::DEFAULT_PREFIX . '_';
        $this->overrideTransactionReference = $overrideRefWithPrefix;
        // create a log channel
        $this->logger = self::$config->getLoggerInstance();

        $this->createReferenceNumber();

        $this->baseUrl = self::$config::BASE_URL;

        $this->logger->notice('Main Class Initializes....');
        return $this;
    }

    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    function setAmount($amount): object
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Sets the allowed payment methods
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both
     * @return object
     * */
    function setPaymentOptions(string $paymentOptions): object
    {
        $this->paymentOptions = $paymentOptions;
        return $this;
    }

    /**
     * Sets the transaction description
     * @param string $customDescription The description of the transaction
     * @return object
     * */
    function setDescription(string $customDescription): object
    {
        $this->customDescription = $customDescription;
        return $this;
    }

    /**
     * Sets the payment page logo
     * @param string $customLogo Your Logo
     * @return object
     * */
    function setLogo(string $customLogo): object
    {
        $this->customLogo = $customLogo;
        return $this;
    }

    /**
     * Sets the payment page title
     * @param string $customTitle A title for the payment. It can be the product name, your business name or anything short and descriptive
     * @return object
     * */
    function setTitle(string $customTitle): object
    {
        $this->customTitle = $customTitle;
        return $this;
    }

    /**
     * Sets transaction country
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     * @return object
     * */
    function setCountry(string $country): object
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Sets the transaction currency
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
     * @return object
     * */
    function setCurrency(string $currency): object
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Sets the customer email
     * @param string $customerEmail This is the paying customer's email
     * @return object
     * */
    function setEmail(string $customerEmail): object
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Sets the customer firstname
     * @param string $customerFirstname This is the paying customer's firstname
     * @return object
     * */
    function setFirstname(string $customerFirstname): object
    {
        $this->customerFirstname = $customerFirstname;
        return $this;
    }

    /**
     * Sets the customer lastname
     * @param string $customerLastname This is the paying customer's lastname
     * @return object
     * */
    function setLastname(string $customerLastname): object
    {
        $this->customerLastname = $customerLastname;
        return $this;
    }


    /**
     * Sets the customer phonenumber
     * @param string $customerPhone This is the paying customer's phonenumber
     * @return object
     * */
    function setPhoneNumber(string $customerPhone): object
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * Sets the payment page button text
     * @param string $payButtonText This is the text that should appear on the payment button on the Rave payment gateway.
     * @return object
     * */
    function setPayButtonText(string $payButtonText): object
    {
        $this->payButtonText = $payButtonText;
        return $this;
    }

    /**
     * Sets the transaction redirect url
     * @param string $redirectUrl This is where the Rave payment gateway will redirect to after completing a payment
     * @return object
     * */
    function setRedirectUrl(string $redirectUrl): object
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     * @param array $meta This are the other information you will like to store with the transaction. It is a key => value array. eg. PNR for airlines, product colour or attributes. Example. array('name' => 'femi')
     * @return object
     * */
    function setMetaData(array $meta): object
    {
        $this->meta = [$this->meta, $meta];
        return $this;
    }


    /**
     * Sets the event hooks for all available triggers
     * @param EventHandlerInterface $handler This is a class that implements the Event Handler Interface
     * @return object
     */
    function eventHandler(EventHandlerInterface $handler): object
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Requerys a previous transaction from the Rave payment gateway
     * @param string $referenceNumber This should be the reference number of the transaction you want to requery
     * @return object
     * */
    function requeryTransaction(string $referenceNumber): object
    {
        $this->txref = $referenceNumber;
        $this->requeryCount++;
        $this->logger->notice('Requerying Transaction....' . $this->txref);
        if (isset($this->handler)) {
            $this->handler->onRequery($this->txref);
        }

        $data = array(
            'id' => (int)$referenceNumber
            // 'only_successful' => '1'
        );

        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json', 'Authorization' => "Bearer ".self::$config->getSecretKey());
        $body = Body::json($data);
        $url = $this->baseUrl . '/transactions/' . $data['id'] . '/verify';
        // Make `POST` request and handle response with unirest
        $response = Request::get($url, $headers);

//         print_r($response);

        //check the status is success
        if ($response->body && $response->body->status === "success") {
            if ($response->body && $response->body->data && $response->body->data->status === "successful") {
                $this->logger->notice('Requeryed a successful transaction....' . json_encode($response->body->data));
                // Handle successful
                if (isset($this->handler)) {
                    $this->handler->onSuccessful($response->body->data);
                }
            } elseif ($response->body && $response->body->data && $response->body->data->status === "failed") {
                // Handle Failure
                $this->logger->warning('Requeryed a failed transaction....' . json_encode($response->body->data));
                if (isset($this->handler)) {
                    $this->handler->onFailure($response->body->data);
                }
            } else {
                // Handled an undecisive transaction. Probably timed out.
                $this->logger->warning('Requeryed an undecisive transaction....' . json_encode($response->body->data));
                // I will requery again here. Just incase we have some devs that cannot setup a queue for requery. I don't like this.
                if ($this->requeryCount > 4) {
                    // Now you have to setup a queue by force. We couldn't get a status in 5 requeries.
                    if (isset($this->handler)) {
                        $this->handler->onTimeout($this->txref, $response->body);
                    }
                } else {
                    $this->logger->notice('delaying next requery for 3 seconds');
                    sleep(3);
                    $this->logger->notice('Now retrying requery...');
                    $this->requeryTransaction($this->txref);
                }
            }
        } else {
            // $this->logger->warn('Requery call returned error for transaction reference.....'.json_encode($response->body).'Transaction Reference: '. $this->txref);
            // Handle Requery Error
            if (isset($this->handler)) {
                $this->handler->onRequeryError($response->body);
            }
        }
        return $this;
    }

    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway
     * @return void
     */
    function initialize(): void
    {
        $this->createCheckSum();

        $this->logger->info("Rendering Payment Modal..");

        echo '<html lang="en">';
        echo '<body>';
//        $loader_img_src = FLW_PHP_ASSET_DIR."js/v3.js";
        echo '<div style="display: flex; flex-direction: row;justify-content: center; align-content: center ">Proccessing...<img src="../assets/images/ajax-loader.gif"  alt="loading-gif"/></div>';
//        $script_src = FLW_PHP_ASSET_DIR."js/v3.js";
        echo '<script type="text/javascript" src="../assets/js/v3.js"></script>';

        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'FlutterwaveCheckout({
            public_key: "' . self::$config->getPublicKey() . '",
            tx_ref: "' . $this->txref . '",
            amount: ' . $this->amount . ',
            currency: "' . $this->currency . '",
            country: "' . $this->country . '",
            payment_options: "card,ussd,mpesa,barter,mobilemoneyghana,mobilemoneyrwanda,mobilemoneyzambia,mobilemoneyuganda,banktransfer,account",
            redirect_url:"' . $this->redirectUrl . '",
            customer: {
              email: "' . $this->customerEmail . '",
              phone_number: "' . $this->customerPhone . '",
              name: "' . $this->customerFirstname . ' ' . $this->customerLastname . '",
            },
            callback: function (data) {
              console.log(data);
            },
            onclose: function() {
                window.location = "?cancelled=cancelled&cancel_ref='.$this->txref.'";
              },
            customizations: {
              title: "' . $this->customTitle . '",
              description: "' . $this->customDescription . '",
              logo: "' . $this->customLogo . '",
            }
        });';
        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';

        $this->logger->info("Rendered Payment Modal Successfully..");

    }

    /**
     * Handle canceled payments with this method
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     * @return object
     * */
    function paymentCanceled(string $referenceNumber): object
    {
        $this->logger->notice('Payment was canceled by user..' . $referenceNumber);
        if (isset($this->handler)) {
            $this->handler->onCancel($referenceNumber);
        }
        return $this;
    }

}

