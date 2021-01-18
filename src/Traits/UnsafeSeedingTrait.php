<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Classes\ConstCustom;

trait UnsafeSeedingTrait
{
    public function clearUnsafePivot()
    {
        if (property_exists($this, 'unsafePivotTables')) {
            foreach ($this->unsafePivotTables as $tableName) {
                \DB::table($tableName)->where(ConstCustom::DB_FIELD_UNSAFE_SEEDING, true)->delete();
            }
        }
    }

    public function scopeUnsafeSeeding($query)
    {
        return $query->where(ConstCustom::DB_FIELD_UNSAFE_SEEDING, true);
    }

    public function isUnsafeSeeding()
    {
        $field = ConstCustom::DB_FIELD_UNSAFE_SEEDING;

        return $this->$field;
    }
}
