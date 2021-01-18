<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxBadParameterPaginateException extends \Exception
{
    public function __construct()
    {
        $exClassName = UtilsCustom::getShortClassName($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message"), ConstCustom::EXCEPTION_BAD_PARAMETER_PAGINATE);
    }
}