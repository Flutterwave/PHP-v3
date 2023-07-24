<?php

declare(strict_types=1);

namespace Flutterwave\Helper;

final class CheckoutHelper
{
    /**
     * Generate Payment Hash.
     *
     * @param array       $payload    the payload.
     * @param string|null $secret_key the secret key.
     *
     * @return string
     */
    public static function generateHash(array $payload, ?string $secret_key = null): string
    {
        // format: sha256(amount+currency+customeremail+txref+sha256(secretkey)).
        // assumes payload has amount, currency, email, and tx_ref.
        $string_to_hash = '';
        foreach ($payload as $value) {
                $string_to_hash .= $value;
        }
        $string_to_hash .= hash('sha256', $secret_key);
        return hash('sha256', $string_to_hash);
    }

    /**
     * Default Payment Methods.
     *
     * @return string
     */
    public static function getDefaultPaymentOptions(): string
    {
        $methods = [
            'account',
            'banktransfer',
            'card',
            'ussd', 
            'barter',
            'mpesa',
            'mobilemoneyghana',
            'mobilemoneyfranco',
            'mobilemoneyuganda',
            'mobilemoneyrwanda',
            'mobilemoneyzambia'
        ];
        
        return implode(',', $methods);
    }

    /**
     * Get Supported Country.
     *
     * @return array
     */
    public static function getSupportedCountry(?string $currency = null): string
    {
        $baseCurrency = 'NGN'; // TODO: allow users to set base currency.
        $countriesMap = array(
            'NGN' => 'NG',
            'EUR' => 'NG',
            'GBP' => 'NG',
            'USD' => 'US',
            'KES' => 'KE',
            'ZAR' => 'ZA',
            'TZS' => 'TZ',
            'UGX' => 'UG',
            'GHS' => 'GH',
            'ZMW' => 'ZM',
            'RWF' => 'RW',
        );

        if (!is_null($currency)) {
            if (! isset($countriesMap[$currency])) {
                throw new \InvalidArgument("The currency $currency is not supported at checkout.");
            }
            return $countriesMap[$currency];
        }

        return $countriesMap[$baseCurrency];
    }
}
