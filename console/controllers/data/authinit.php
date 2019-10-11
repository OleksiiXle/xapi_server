<?php
return [
    'permissions' => [

        //***************************************************************** Общие роуты
        'menuAll'                          => 'Загальні пункти меню',

        //***************************************************************** Администрирование
        'menuAdminxMain'          => 'Системне адміністрування (меню)',
        'systemAdminxx'             => 'Системне адміністрування (дії)',

    ],
    'roles' => [
        'superAdmin' => 'Головний системний адміністратор',
        'user' => 'User',
    ],
    'rolesPermissions' => [
        'superAdmin' => [
            'menuAdminxMain',
            'systemAdminxx',
        ],
    ],
    'rolesChildren' => [
        'superAdmin' => [
            'user',
        ],
    ]
];