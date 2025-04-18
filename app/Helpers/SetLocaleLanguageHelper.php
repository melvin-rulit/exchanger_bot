<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocaleLanguageHelper
{
    public static function setAppLanguage($language): void
    {
        App::setLocale($language);
    }
    public static function getAppLanguage($language): void
    {
        Log::alert(app()->getLocale());
    }
}
