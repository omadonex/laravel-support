<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxMethodNotFoundInClassException extends \Exception
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
        ]), ConstCustom::EXCEPTION_METHOD_NOT_FOUND_IN_CLASS);
    }
}