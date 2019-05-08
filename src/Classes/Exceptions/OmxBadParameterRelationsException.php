<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class OmxBadParameterRelationsException extends \Exception
{
    protected $availableRelations;

    public function __construct($availableRelations)
    {
        $this->availableRelations = $availableRelations;

        $relationsStr = implode(", ", $availableRelations);
        $exClassName = get_class($this);
        parent::__construct(trans("support::exceptions.{$exClassName}.message", [
            'relations' => $relationsStr,
        ]), ConstantsCustom::EXCEPTION_BAD_PARAMETER_RELATIONS);
    }
}