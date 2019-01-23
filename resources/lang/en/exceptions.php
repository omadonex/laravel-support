<?php

return [
    'OmxBadParameterActiveException' => [
        'message' => 'Param `active` can be one of next values: false | true',
    ],
    'OmxBadParameterPaginateException' => [
        'message' => 'Param `paginate` can be one of next values: "false | true | integer"',
    ],
    'OmxBadParameterRelationsException' => [
        'message' => 'Param `relations` can be one of next values: "false | true | array (:relations)"',
    ],
    'OmxBadParameterTrashedException' => [
        'message' => 'Param `trashed` can be one of next values: "with | only"',
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
];