<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

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
        ]));
    }
}