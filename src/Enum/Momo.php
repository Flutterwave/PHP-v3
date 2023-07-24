<?php

declare(strict_types=1);

namespace Flutterwave\Enum;

//use Cerbero\Enum\Concerns\Enumerates;

enum Momo: string
{
    //    use Enumerates;
    case GHANA = 'mobile_money_ghana';
    case UGANDA = 'mobile_money_uganda';
    case FRANCO = 'mobile_money_franco';
    case RWANDA = 'mobile_money_rwanda';
    case ZAMBIA = 'mobile_money_zambia';
}
