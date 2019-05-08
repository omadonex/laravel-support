<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxBadParameterTrashedException extends \Exception
{
    public function __construct()
    {
        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message"), ConstantsCustom::EXCEPTION_BAD_PARAMETER_TRASHED);
    }
}