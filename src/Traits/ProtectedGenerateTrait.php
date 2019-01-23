<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

trait ProtectedGenerateTrait
{
    public function scopeProtectedGenerate($query)
    {
        return $query->where(ConstantsCustom::DB_FIELD_PROTECTED_GENERATE, true);
    }

    public function isProtectedGenerate()
    {
        $field = ConstantsCustom::DB_FIELD_PROTECTED_GENERATE;

        return $this->$field;
    }
}
