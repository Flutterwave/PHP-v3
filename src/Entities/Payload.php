<?php

namespace Flutterwave\Entities;

use Flutterwave\Util\AuthMode;
use Flutterwave\Contract\EntityInterface;

class Payload implements EntityInterface
{
    public const PIN = 'pin';
    public const OTP = 'otp';
    public const REDIRECT = 'redirect';
    public const NOAUTH = 'noauth';
    public const AVS = 'avs';

    protected array $data = [];

    protected ?string $type = null;

    public function get(string $param)
    {
        if (! $this->has($param)) {
            return null;
        }
        return $this->data[$param];
    }

    public function set(string $param, $value): void
    {
        if ($param === AuthMode::PIN) {
            $this->data['otherData']['authorization']['mode'] = self::PIN;
            $this->data['otherData']['authorization'][AuthMode::PIN] = $value;
        } else {
            $this->data[$param] = $value;
        }
    }

    public function delete(string $param, array $assoc_option = []): void
    {
        if (! isset($param)) {
            return;
        }

        if ($param === 'otherData' && count($assoc_option) > 0) {
            foreach ($assoc_option as $option) {
                unset($this->data['otherData'][$option]);
            }
        }
        unset($this->data[$param]);
    }

    public function setPayloadType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function toArray(?string $payment_method = null): array
    {
        $data = $this->data;
        $customer = $data['customer'] ?? new Customer();
        $additionalData = $data['otherData'] ?? [];

        if (gettype($customer) === 'string') {
            $string_value = $customer;
            $customer = new Customer();
            $customer->set('customer', $string_value);
        }

        switch ($payment_method) {
        case 'card':
            $card_details = $additionalData['card_details'];
            unset($additionalData['card_details']);
            $data = array_merge($data, $additionalData, $customer->toArray(), $card_details);
            break;
        case 'account':
            $account_details = $additionalData['account_details'];
            unset($additionalData['account_details']);
            $data = array_merge($data, $additionalData, $customer->toArray(), $account_details);
            break;
        default:
            $data = array_merge($data, $additionalData, $customer->toArray());
            break;
        }

        if ($payment_method === 'modal') {
            return $data;
        }

        unset($data['customer']);
        unset($data['otherData']);

        //convert customer obj to array
        $data = array_merge($additionalData, $data, $customer->toArray());

        //if $data['preauthorize'] is false unset
        if (isset($data['preauthorize']) && empty($data['preauthorize'])) {
            unset($data['preauthorize']);
        }

        if (array_key_exists('phone_number', $data) && is_null($data['phone_number'])) {
            unset($data['phone_number']);
        }

        //if $data['payment_plan'] is null unset
        if (isset($data['payment_plan']) && empty($data['payment_plan'])) {
            unset($data['payment_plan']);
        }
        return $data;
    }

    public function update($param, $value): void
    {
        if ($param === 'otherData' && \is_array($value)) {
            foreach ($value as $key => $item) {
                $this->data['otherData'][$key] = $item;
            }
        }

        $this->data = array_merge($this->data, [$param => $value]);
    }

    public function empty(): void
    {
        $this->data = [];
    }

    public function has(string $param): bool
    {
        if (! isset($this->data[$param])) {
            return false;
        }
        return true;
    }

    public function size(): int
    {
        return count($this->data);
    }

    public function generateTxRef(): void
    {
        if ($this->has('tx_ref')) {
            $this->set('tx_ref', 'FLWPHP|' . (mt_rand(2, 101) + time()));
        }
    }
}
