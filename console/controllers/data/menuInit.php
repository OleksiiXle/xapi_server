<?php
$t = [
    [
        'name' => 'Адміністрування',
        'route' => '',
        'role' => 'menuAdminxMain',
        'access_level' => 2,
        'children' => [
            [
                'name'       => 'Користувачі',
                'route'      => '/adminx/user',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Правила',
                'route'      => '/adminx/rule',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Дозвіли, ролі',
                'route'      => '/adminx/auth-item',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Редактор меню',
                'route'      => '/adminx/menux/menu',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Системні налаштування',
                'route'      => '/adminx/configs/update',
                'role'        => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Відвідування сайту',
                'route'      => '/adminx/check/guest-control',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'PHP-info',
                'route'      => 'adminx/user/php-info',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => [],
            ],
        ]
    ],
    [
        'name' => 'Кино',
        'route' => '',
        'role' => 'menuAdminxMain',
        'access_level' => 2,
        'children' => [
            [
                'name'       => 'Кинозалы',
                'route'      => '/kino/hall/index',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
            [
                'name'       => 'Сеансы',
                'route'      => '/kino/seans/index',
                'role'       => 'menuAdminxMain',
                'access_level' => 2,
                'children' => []
            ],
        ]
    ],

    //********************************************************************************************************** КАБИНЕТ
    [
        'name' => 'Кабінет',
        'route' => '',
        'role' => 'menuAll',
        'access_level' => 0,
        'children' => [
            [
                'name'       => 'Зміна паролю',
                'route'      => '/adminx/user/change-password',
                'role' => 'menuAll',
                'access_level' => 0,
                'children' => [],
            ],
            [
                'name'       => 'Вихід',
                'route'      => '/site/logout ',
                'role' => 'menuAll',
                'access_level' => 0,
                'children' => [],
            ],
        ]
    ],
    [
        'name'       => 'Вхід',
        'route'      => '/site/login',
        'role' => '',
        'access_level' => 0,
        'children' => [],
    ],
];

return $t;