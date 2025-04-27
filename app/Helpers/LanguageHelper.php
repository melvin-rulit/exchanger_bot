<?php

if (!function_exists('setAppLanguage')) {
    function setAppLanguage($language): void
    {
        App::setLocale($language);
    }
}

if (!function_exists('getAppLanguage')) {
    function getAppLanguage($language): void
    {
        Log::alert(app()->getLocale());
    }
}
