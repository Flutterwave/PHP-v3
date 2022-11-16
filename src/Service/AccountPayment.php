<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Exception;
use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\AccountEventHandler;
use Flutterwave\Payload;
use Flutterwave\Traits\Group\Charge;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use stdClass;

class AccountPayment extends Service implements Payment
{
    use Charge;
    public const ENDPOINT = 'charge';
    public const DEBIT_NG = 'debit_ng_account';
    public const DEBIT_UK = 'debit_uk_account';
    public const TYPE = 'account';
    protected array $accounts = [
        'NG' => self::DEBIT_NG,
        'UK' => self::DEBIT_UK,
    ];
    protected string $country = 'NG';
    private AccountEventHandler $eventHandler;

    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();

        $this->url = $this->baseUrl.'/'.$endpoint.'?type=';
        $this->eventHandler = new AccountEventHandler();
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
        if ($this->checkPayloadIsValid($payload, 'account_details')) {
            return $this->charge($payload);
        }
        $msg = 'Account Service:Please pass account details.';
        $this->logger->info($msg);
        throw new InvalidArgumentException($msg);
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function charge(Payload $payload): array
    {
        $this->logger->notice('Account Service::Charging Account ...');

        $this->checkSpecialCasesParams($payload);
        $payload = $payload->toArray(self::TYPE);

        //request payload
        $body = $payload;

        //check which country was passed.
        $account = $this->accounts[$payload['country']];

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
        $banks = require __DIR__ . '/../Util/unique_bank_cases.php';

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
     * @param array $payload
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
