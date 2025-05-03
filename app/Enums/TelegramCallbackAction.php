<?php

namespace App\Enums;

enum TelegramCallbackAction: string
{
    // Menu
    case SelectCountry  = 'select_country';
    case SelectBank     = 'select_bank';
    case InputAmount     = 'input_amount';
    case SelectCurrency     = 'select_currency';
    case SelectLanguage   = 'select_language';


    // Back
    case SelectCountryBack = 'select_country_back';
    case SelectBankBack    = 'select_bank_back';
    case SelectAmountBack    = 'select_amount_back';
    case SelectCurrencyBack = 'select_currency_back';

    // Go To
    case ToMain           = 'to_main';
    case ToConsultation   = 'to_consultation_';
}
