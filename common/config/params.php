<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,

    'menuType' => 'vertical',
    'userControl' => '1',
    'guestControl' => '1',
    'guestControlDuration' => '3600',
    'permCacheKey' => 'perm',
    'permCacheKeyDuration' => '180',
    'passwordResetTokenExpire' => '3600',
    'userDefaultRole' => 'user',
    'rbacCacheSource' => 'session',
    'defaultRoles' => [
        'superAdmin' => 'superAdmin',
        'user' => 'user',
    ]

];
