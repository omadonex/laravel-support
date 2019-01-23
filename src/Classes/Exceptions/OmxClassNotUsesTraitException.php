<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

class OmxClassNotUsesTraitException extends \Exception
{
    protected $className;
    protected $traitName;

    public function __construct($className, $traitName)
    {
        $this->className = $className;
        $this->traitName = $traitName;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'class' => $className,
            'trait' => $traitName,
        ]));
    }
}