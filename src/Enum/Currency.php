<?php

declare(strict_types=1);

namespace Flutterwave\Enum;

//use Cerbero\Enum\Concerns\Enumerates;

enum Currency:string
{
//    use Enumerates;
    case NGN = 'NGN';
    case USD = 'USD';
    case KES = 'KES';
    case ZAR = 'ZAR';
    case ZMW = 'ZMW';
    case EUR = 'EUR';
    case GHS = 'GHS';
    case TNZ = 'TNZ';
    case RWF = 'RWF';
    case XAF = 'XAF';
    case XOF = 'XOF';
}
