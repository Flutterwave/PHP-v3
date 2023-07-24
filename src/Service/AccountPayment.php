<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Exception;
use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\AccountEventHandler;
use Flutterwave\Entities\Payload;
use Flutterwave\Traits\Group\Charge;
use Flutterwave\Util\Currency;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

class AccountPayment extends Service implements Payment
{
    use Charge;

    public const ENDPOINT = 'charge';
    public const DEBIT_NG = 'mono';
    public const DEBIT_UK = 'account-ach-uk';
    public const TYPE = 'account';
    protected array $accounts = [
        Currency::NGN => self::DEBIT_NG,
        Currency::GBP => self::DEBIT_UK,
        Currency::EUR => self::DEBIT_UK
     ];
    protected string $country = 'NG';
    private AccountEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();

        $this->url = $this->baseUrl . '/' . $endpoint . '?type=';
        $this->eventHandler = new AccountEventHandler($config);
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
     * @throws Exception
     * @throws GuzzleException
     */
    public function initiate(Payload $payload): array
    {
        if($payload->has('currency') && !key_exists($payload->get('currency'), $this->accounts)) {
            $msg = 'Account Service: The Currency passed is not supported. kindy pass NGN, GBP or EUR.';
            $this->logger->info($msg);
            throw new InvalidArgumentException($msg);
        }

        if ($this->checkPayloadIsValid($payload, 'account_details')) {
            return $this->charge($payload);
        }
        $msg = 'Account Service:Please pass account details.';
        $this->logger->info($msg);
        throw new InvalidArgumentException($msg);
    }

    /**
     * @param  Payload $payload
     * @return array
     *
     * @throws ClientExceptionInterface
     */
    public function charge(Payload $payload): array
    {
        $this->logger->notice('Account Service::Charging Account ...');

        if($payload->has('currency') &&   $payload->get('currency') === Currency::NGN ) {
            $this->checkSpecialCasesParams($payload);
        }

        $payload = $payload->toArray(self::TYPE);

        //request payload
        $body = $payload;

        //check which country was passed.
        $account = $this->accounts[$payload['currency']];

        unset($body['country']);
        unset($body['address']);

        AccountEventHandler::startRecording();
        $request = $this->request($body, 'POST', $account);
        AccountEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback): void
    {
        call_user_func_array($callback, []);
    }

    private function checkSpecialCasesParams(Payload $payload)
    {
        $details = $payload->get('otherData')['account_details'];
        $banks = include __DIR__ . '/../Util/unique_bank_cases.php';

        foreach ($banks as $code => $case) {
            if ($details['account_bank'] === $code) {
                $key = array_keys($case['requiredParams'])[0]; //assuming required param is one

                if (! isset($details[$key])) {
                    $this->logger->notice("Account Service:: {$key} is required for the request");
                    throw new InvalidArgumentException("{$key} is required for the request");
                }

                if ($key === 'bvn') {
                    $bvn = $details[$key];
                    $pattern = '/([0-9]){11}/';
                    return preg_match_all($pattern, $bvn);
                }

                if ($key === 'passcode') {
                    $passcode = $details[$key];
                    $pattern = '/([0-9]){8}/';
                    return preg_match_all($pattern, $passcode);
                }
            }
        }
        return true;
    }

    /**
     * @param stdClass $response
     * @param array    $payload
     *
     * @return array
     *
     * @throws Exception
     */
    private function handleAuthState(stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger]);
    }
}
