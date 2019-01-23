<?php

namespace Omadonex\LaravelSupport\Classes\Exceptions;

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
        ]));
    }
}