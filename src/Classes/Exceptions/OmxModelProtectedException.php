<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxModelProtectedException extends \Exception
{
    protected $model;

    public function __construct($model)
    {
        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'class' => get_class($model),
        ]), ConstantsCustom::EXCEPTION_MODEL_PROTECTED);
    }
}