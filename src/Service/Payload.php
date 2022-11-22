<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Payload as Load;

class Payload
{
    protected array $requiredParams = [
        'amount','tx_ref','currency','customer',
    ];

    public function create(array $data): Load
    {
        $check = $this->validSuppliedData($data);
        if (! $check['result']) {
            throw new \InvalidArgumentException("<b><span style='color:red'>".$check['missing_param'].'</span></b>'.' is required in the payload');
        }

        $currency = $data['currency'];
        $amount = $data['amount'];
        $customer = $data['customer'];
        $redirectUrl = $data['redirectUrl'] ?? null;
        $otherData = $data['additionalData'] ?? null;
        $phone_number = $data['phone'] ?? null;

        if (isset($data['pin']) && ! empty($data['pin'])) {
            $otherData['pin'] = $data['pin'];
        }

        $payload = new Load();

        if (! \is_null($phone_number)) {
            $payload->set('phone', $phone_number);
        }

        $tx_ref = $data['tx_ref'] ?? $payload->generateTxRef();

//        $payload->set('phone_number', $phone_number); // customer factory handles that
        $payload->set('currency', $currency);
        $payload->set('amount', $amount);
        $payload->set('tx_ref', $tx_ref);
        $payload->set('customer', $customer);
        $payload->set('redirect_url', $redirectUrl);
        $payload->set('otherData', $otherData);

        return $payload;
    }

    public function validSuppliedData(array $data): array
    {
        $params = $this->requiredParams;

        foreach ($params as $param) {
            if (! array_key_exists($param, $data)) {
                return ['missing_param' => $param, 'result' => false];
            }
        }

        if (! $data['customer'] instanceof \Flutterwave\Customer) {
            return ['missing_param' => 'customer', 'result' => false];
        }

        return ['missing_param' => null, 'result' => true];
    }
}
