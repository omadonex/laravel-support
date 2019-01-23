<?php

namespace Omadonex\LaravelSupport\Classes\Utils;

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

class UtilsDb
{
    public static function addPrimaryStr($table)
    {
        $table->string('id', ConstantsCustom::DB_FIELD_LEN_PRIMARY_STR);
        $table->primary('id');
    }

    public static function addTransFields($table, $primaryStr = false)
    {
        $fieldName = ConstantsCustom::DB_FIELD_TRANS_MODEL_ID;
        if ($primaryStr) {
            $table->string($fieldName, ConstantsCustom::DB_FIELD_LEN_PRIMARY_STR)->index();
        } else {
            $table->unsignedInteger($fieldName)->index();
        }

        $table->string(ConstantsCustom::DB_FIELD_TRANS_LANG, ConstantsCustom::DB_FIELD_LEN_LANG)->index();

        $table->unique([$fieldName, ConstantsCustom::DB_FIELD_TRANS_LANG], 'trans_unique');
    }

    public static function addUnsafeSeedingField($table)
    {
        $table->boolean(ConstantsCustom::DB_FIELD_UNSAFE_SEEDING)->default(false)->index();
    }

    public static function addProtectedGenerateField($table)
    {
        $table->boolean(ConstantsCustom::DB_FIELD_PROTECTED_GENERATE)->default(false)->index();
    }
}