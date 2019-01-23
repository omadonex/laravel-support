<?php

return [
    'OmxBadParameterActiveException' => [
        'message' => 'Параметр `active` может принимать одно из следующих значений: false | true',
    ],
    'OmxBadParameterPaginateException' => [
        'message' => 'Параметр `paginate` может принимать одно из следующих значений: "false | true | integer"',
    ],
    'OmxBadParameterRelationsException' => [
        'message' => 'Параметр `relations` может принимать одно из следующих значений: "false | true | array (:relations)"',
    ],
    'OmxBadParameterTrashedException' => [
        'message' => 'Параметр `trashed` может принимать одно из следующих значений: "with | only"',
    ],
    'OmxClassNotUsesTraitException' => [
        'message' => 'Класс `:class` не использует trait `:trait`',
    ],
    'OmxMethodNotFoundInClassException' => [
        'message' => 'Метод `:method` не найден в классе `:class`',
    ],
    'OmxMethodNotImplementedInClassException' => [
        'message' => 'Интерфейсный метод `:method` не имеет реализации в классе `:class`',
    ],
    'OmxModelNotSearchedException' => [
        'message' => 'Запись в таблице `:table` с заданными условиями не найдена (модель `:class`)',
    ],
    'OmxModelNotSmartFoundException' => [
        'message' => 'Запись в таблице `:table` с `:field`=:value не найдена (модель `:class`)',
    ],
    'OmxModelProtectedException' => [
        'message' => 'Запись защищена и не может быть изменена (модель `:class`)',
    ],
];