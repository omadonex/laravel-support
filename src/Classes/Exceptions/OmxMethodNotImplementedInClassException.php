<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxMethodNotImplementedInClassException extends \Exception
{
    protected $className;
    protected $methodName;

    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;

        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'method' => $methodName,
            'class' => $className,
        ]), ConstantsCustom::EXCEPTION_METHOD_NOT_IMPLEMENTED_IN_CLASS);
    }
}