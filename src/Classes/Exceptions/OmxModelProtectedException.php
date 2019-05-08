<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxModelProtectedException extends \Exception
{
    protected $model;

    public function __construct($model)
    {
        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'class' => get_class($model),
        ]), ConstantsCustom::EXCEPTION_MODEL_PROTECTED);
    }
}