<?php

if (!function_exists('mainDataGlobal'))
{
    function mainDataGlobal()
    {
        $key = \Omadonex\LaravelSupport\Classes\ConstantsCustom::MAIN_DATA_GLOBAL;

        return $$key;
    }
}

if (!function_exists('mainDataPage'))
{
    function mainDataGlobal()
    {
        $key = \Omadonex\LaravelSupport\Classes\ConstantsCustom::MAIN_DATA_PAGE;

        return $$key;
    }
}