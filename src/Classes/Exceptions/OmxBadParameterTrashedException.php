<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

class OmxBadParameterTrashedException extends \Exception
{
    public function __construct()
    {
        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message"));
    }
}