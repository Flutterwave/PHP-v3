<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Exception;
use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\AchEventHandler;
use Flutterwave\Entities\Payload;
use Flutterwave\Traits\Group\Charge;
use Flutterwave\Util\Currency;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class AchPayment extends Service implements Payment
{
    use Charge;

    public const TYPE = 'ach_payment';

    protected string $country = 'US';
    protected array $currency = [
        Currency::USD => 'US',
        Currency::ZAR => 'ZA',
    ];
    private AchEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();
        $this->url = $this->baseUrl . '/' . $endpoint . '?type=';
        $this->eventHandler = new AchEventHandler($config);
    }

    public function setCountry(string $country): void
    {
        if ($this->country !== $country) {
            $this->country = $country;
        }
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     */
    public function initiate(Payload $payload): array
    {
        return $this->charge($payload);
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function charge(Payload $payload): array
    {
        $this->logger->notice(' Ach Service::Started Charging Account ...');

        $currency = $payload->get('currency');

        $this->setCountry($this->currency[$currency]);

        $payload->set('country', $this->country);

        $payload = $payload->toArray();

        //request payload
        $body = $payload;

        unset($body['address']);

        AchEventHandler::startRecording();
        $request = $this->request($body, 'POST', self::TYPE);
        AchEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws Exception
     */
    private function handleAuthState(stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger]);
    }
}
