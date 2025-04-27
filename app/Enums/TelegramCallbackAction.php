<?php

namespace App\Enums;

enum TelegramCallbackAction: string
{

    case SelectCountry  = 'select_country';
    case SelectBank     = 'select_bank';
    case InputAmount     = 'input_amount';
    case SelectCurrency     = 'select_currency';


    // Back
    case SelectCountryBack = 'select_country_back';
    case SelectBankBack    = 'select_bank_back';
    case SelectCurrencyBack = 'select_currency_back';

    // Go To
    case ToMain           = 'to_main';
    case SelectLanguage   = 'select_language';
}
