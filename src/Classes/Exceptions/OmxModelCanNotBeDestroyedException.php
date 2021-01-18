<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;

class OmxModelCanNotBeDestroyedException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message, ConstCustom::EXCEPTION_MODEL_CAN_NOT_BE_DESTROYED);
    }
}