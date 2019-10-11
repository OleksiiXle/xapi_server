<?php
return [
    [
        'username' => 'admin',
        'email' => 'admin1@email.com',
        'password' => '1234567890',
        'retypePassword' => '1234567890',
        'first_name' => 'Главный',
        'middle_name' => 'Системный',
        'last_name' => 'Администратор',
        'userRoles' => [
            'superAdmin'
        ],
    ],
    [
        'username' => 'user',
        'email' => 'admin2@email.com',
        'password' => '1234567890',
        'retypePassword' => '1234567890',
        'first_name' => 'Администратор',
        'middle_name' => 'Человеческих',
        'last_name' => 'Ресурсов',
        'userRoles' => [
            'user'
        ],
    ],
];
