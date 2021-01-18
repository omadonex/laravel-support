<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class OmxModelProtectedException extends \Exception
{
    protected $model;

    public function __construct($model)
    {
        $exClassName = UtilsCustom::getShortClassName($this);;
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'class' => get_class($model),
        ]), ConstCustom::EXCEPTION_MODEL_PROTECTED);
    }
}