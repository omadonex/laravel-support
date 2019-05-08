<?php

use Omadonex\LaravelSupport\Classes\ConstantsCustom;

return [
    'OmxBadParameterEnabledException' => [
        'message' => 'Param `' . ConstantsCustom::REQUEST_PARAM_ENABLED . '` can be one of next values: false | true',
    ],
    'OmxBadParameterPaginateException' => [
        'message' => 'Param `' . ConstantsCustom::REQUEST_PARAM_PAGINATE . '` can be one of next values: "false | true | integer"',
    ],
    'OmxBadParameterRelationsException' => [
        'message' => 'Param `' . ConstantsCustom::REQUEST_PARAM_RELATIONS . '` can be one of next values: "false | true | array (:relations)"',
    ],
    'OmxBadParameterTrashedException' => [
        'message' => 'Param `' . ConstantsCustom::REQUEST_PARAM_TRASHED . '` can be one of next values: "with | only"',
    ],
    'OmxClassNotUsesTraitException' => [
        'message' => 'Class `:class` not uses trait `:trait`',
    ],
    'OmxMethodNotFoundInClassException' => [
        'message' => 'Method `:method` not found in class `:class`',
    ],
    'OmxMethodNotImplementedInClassException' => [
        'message' => 'Interface method `:method` doesn`t have implementation in class `:class`',
    ],
    'OmxModelNotSearchedException' => [
        'message' => 'Record in table `:table` with this query not found (model `:class`)',
    ],
    'OmxModelNotSmartFoundException' => [
        'message' => 'Record in table `:table` with `:field`=:value not found (model `:class`)',
    ],
    'OmxModelProtectedException' => [
        'message' => 'Record is protected and cannot be changed (model `:class`)',
    ],
    'OmxShellException' => [
        'message' => 'Shell script execution error (result: `:result`)'
    ],
];