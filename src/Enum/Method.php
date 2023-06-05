<?php

declare(strict_types=1);

namespace Flutterwave\Enum;

//use Cerbero\Enum\Concerns\Enumerates;

enum Method: string
{
    //    use Enumerates;
    case DEFAULT = 'default';
    case STANDARD = 'standard';
    case CARD = 'card';
    case MOMO = 'momo';
    case USSD = 'ussd';
    case ACH = 'ach';
    case TRANSFER = 'transfer';
    case MPESA = 'mpesa';
    case PAYPAL = 'paypal';
}
