<?php
namespace Flutterwave\Service;

use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\AchEventHandler;
use Flutterwave\Helper\Config;
use Flutterwave\Traits\Group\Charge;
use Unirest\Exception;

class AchPayment extends Service implements Payment
{
    use Charge;

    const TYPE = "ach_payment";
    const USD = "USD";
    const ZAR = "ZAR";
    protected string $country = "US";
    protected array $currency = [
        self::USD => "US",
        self::ZAR => "ZA"
    ];
    private AchEventHandler $eventHandler;

    function __construct(Config $config)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url  = $this->baseUrl."/".$endpoint."?type=";
        $this->eventHandler = new AchEventHandler();
    }

    public function setCountry(string $country):void
    {
        if($this->country !== $country)
        {
            $this->country = $country;
        }
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload): array
    {
        return $this->charge($payload);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function charge(\Flutterwave\Payload $payload): array
    {
        $this->logger->notice(" Ach Service::Started Charging Account ...");

        $currency = $payload->get("currency");

        $this->setCountry($this->currency[$currency]);

        $payload->set('country', $this->country);

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['address']);

        AchEventHandler::startRecording();
        $request = $this->request($body,'POST', self::TYPE);
        AchEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback)
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws \Exception
     */
    private function handleAuthState(\stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger] );
    }
}
