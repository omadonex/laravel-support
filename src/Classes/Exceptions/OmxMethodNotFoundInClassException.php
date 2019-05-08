<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxMethodNotFoundInClassException extends \Exception
{
    protected $className;
    protected $methodName;

    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'method' => $methodName,
            'class' => $className,
        ]), ConstantsCustom::EXCEPTION_METHOD_NOT_FOUND_IN_CLASS);
    }
}