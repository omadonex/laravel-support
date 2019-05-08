<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxBadParameterTrashedException extends \Exception
{
    public function __construct()
    {
        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message"), ConstantsCustom::EXCEPTION_BAD_PARAMETER_TRASHED);
    }
}