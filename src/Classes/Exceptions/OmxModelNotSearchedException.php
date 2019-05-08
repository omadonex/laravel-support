<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxModelNotSearchedException extends \Exception
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'table' => $model->getTable(),
            'class' => get_class($model),
        ]), ConstantsCustom::EXCEPTION_MODEL_NOT_SEARCHED);
    }
}