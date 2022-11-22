<?php

declare(strict_types=1);

namespace Flutterwave\Enum;

//use Cerbero\Enum\Concerns\Enumerates;

enum Bill:string
{
//    use Enumerates;
    case AIRTIME = 'AIRTIME';
    case DSTV = 'DSTV';
    case DSTV_BOX_OFFICE = 'DSTV BOX OFFICE';
    case POSTPAID = 'Postpaid';
    case PREPAID = 'Prepaid';
    case AIRTEL = 'AIRTEL';
    case IKEDC_TOP_UP = 'IKEDC TOP UP';
    case EKEDC_POSTPAID_TOPUP = 'EKEDC POSTPAID TOPUP';
    case EKEDC_PREPAID_TOPUP = 'EKEDC PREPAID TOPUP';
    case LCC = 'LCC';
    case KADUNA_TOP_UP = 'KADUNA TOP UP';
}
