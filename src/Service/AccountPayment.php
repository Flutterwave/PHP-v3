<?php
namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;
use Flutterwave\Contract\Payment;
use Flutterwave\EventHandlers\AccountEventHandler;
use Flutterwave\Traits\Group\Charge;
use InvalidArgumentException;
use Unirest\Exception;

class AccountPayment extends Service implements Payment
{
    use Charge;
    const ENDPOINT = "charge";
    const DEBIT_NG = "debit_ng_account";
    const DEBIT_UK = "debit_uk_account";
    protected array $accounts = [
        "NG" => self::DEBIT_NG,
        "UK" => self::DEBIT_UK
    ];
    protected string $country = "NG";
    const TYPE = 'account';
    private string $end_point;
    private AccountEventHandler $eventHandler;

    function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);

        $endpoint = $this->getEndpoint();

        $this->url  = $this->baseUrl."/".$endpoint."?type=";
        $this->end_point = self::ENDPOINT."?type=".self::TYPE;
        $this->eventHandler = new AccountEventHandler();
    }

    public function setCountry(string $country):void
    {
        if($this->country !== $country)
        {
            $this->country = $country;
        }
    }

    private function checkSpecialCasesParams(\Flutterwave\Payload $payload)
    {
        $details = $payload->get('otherData')['account_details'];
        $banks = require __DIR__ . "/../Util/unique_bank_cases.php";

        foreach ( $banks as $code => $case )
        {
            if($details['account_bank'] == $code){
                $key = array_keys($case['requiredParams'])[0]; //assuming required param is one

                if(!isset($details[$key]))
                {
                    $this->logger->notice("Account Service:: {$key} is required for the request");
                    throw new InvalidArgumentException("{$key} is required for the request");
                }

                if($key == 'bvn')
                {
                    $bvn = $details[$key];
                    $pattern = '/([0-9]){11}/';
                    return preg_match_all($pattern,$bvn);
                }

                if($key == 'passcode')
                {
                    $passcode = $details[$key];
                    $pattern = '/([0-9]){8}/';
                    return preg_match_all($pattern,$passcode);
                }
            }

            return true;
        }
    }

    /**
     * @throws Exception
     */
    public function initiate(\Flutterwave\Payload $payload): array
    {
        if($this->checkPayloadIsValid($payload, "account_details"))
        {
            return $this->charge($payload);
        }
        $msg = "Account Service:Please pass account details.";
        $this->logger->info($msg);
        throw new InvalidArgumentException($msg);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function charge(\Flutterwave\Payload $payload): array
    {
        $this->logger->notice("Account Service::Charging Account ...");

        $this->checkSpecialCasesParams($payload);
        $payload = $payload->toArray(self::TYPE);

        //request payload
        $body = $payload;

        //check which country was passed.
        $account = $this->accounts[$payload['country']];

        unset($body['country']);
        unset($body['address']);

        AccountEventHandler::startRecording();
        $request = $this->request($body,'POST', $account);
        AccountEventHandler::setResponseTime();
        return $this->handleAuthState($request, $body);
    }

    public function save(callable $callback)
    {
        call_user_func_array($callback, []);
    }

    /**
     * @throws \Exception
     */
    private function handleAuthState(\stdClass $response, array $payload): array
    {
        return $this->eventHandler->onAuthorization($response, ['logger' => $this->logger] );
    }
}

