<?php

namespace App\Enums;

enum MenuLevelStatus:string
{
case Consultant = 'consultant_input';
case Amount = 'amount_input';
case Currency = 'currency_input';

case Country = 'country_input';

case Screenshot = 'send_screenshot';
case Bank = 'bank_input';
}
