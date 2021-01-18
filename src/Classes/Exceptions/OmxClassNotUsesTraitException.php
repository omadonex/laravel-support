<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxClassNotUsesTraitException extends \Exception
{
    protected $className;
    protected $traitName;

    public function __construct($className, $traitName)
    {
        $this->className = $className;
        $this->traitName = $traitName;

        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'class' => $className,
            'trait' => $traitName,
        ]), ConstCustom::EXCEPTION_CLASS_NOT_USES_TRAIT);
    }
}