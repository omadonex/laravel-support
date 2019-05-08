<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxModelCanNotBeDisabledException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message, ConstantsCustom::EXCEPTION_MODEL_CAN_NOT_BE_DISABLED);
    }
}