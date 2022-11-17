<?php

declare(strict_types=1);

namespace Flutterwave\Traits;

use Exception;
use Flutterwave\Contract;
use InvalidArgumentException;

trait PaymentFactory
{
    /**
     * @throws Exception
     */
    public static function create(string $payment): Contract\Payment
    {
        if (is_null(self::$methods)) {
            throw new Exception('No Payment Method Available at the moment. Please reach out to support');
        }

        if (! array_key_exists($payment, self::$methods)) {
            throw new InvalidArgumentException('Please use a valid payment method');
        }

        return new self::$methods[$payment](self::$config);
    }
}
