<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxUnexpectedException extends \Exception
{
    protected $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]), ConstantsCustom::EXCEPTION_UNEXPECTED);
    }
}