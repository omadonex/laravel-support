<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxModelNotSmartFoundException extends \Exception
{
    protected $model;
    protected $value;
    protected $field;

    public function __construct($model, $value, $field)
    {
        $this->model = $model;
        $this->value = $value;
        $this->field = $field;

        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'table' => $model->getTable(),
            'field' => $field,
            'value' => $value,
            'class' => get_class($model),
        ]), ConstantsCustom::EXCEPTION_MODEL_NOT_SMART_FOUND);
    }
}