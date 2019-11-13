<?php

namespace Omadonex\LaravelSupport\Classes\Utils;

class UtilsSeo
{
    public static function detectBot($userAgent)
    {
        return (bool) preg_match('/YandexBot/i', $userAgent);
    }
}