<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxUnexpectedException extends \Exception
{
    protected $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;

        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]), ConstCustom::EXCEPTION_UNEXPECTED);
    }
}