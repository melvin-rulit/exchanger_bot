<?php

namespace App\Enums;

enum MenuLevelStatus:string
{
    case Country = 'country_input';
    case Bank = 'bank_input';
    case Consultant = 'consultant_input';
    case Amount = 'amount_input';
    case Currency = 'currency_input';
    case Requisite = 'requisite_input';
    case Screenshot = 'send_screenshot';
    case Wallet = 'send_wallet';
}
