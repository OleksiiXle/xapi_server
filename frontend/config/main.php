<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'v1' => [
            'class' => 'frontend\modules\v1\V1',
        ],
        'oauth2' => [
            'class' => 'frontend\modules\oauth2\Module',
        ],
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'class' => 'frontend\modules\oauth2\models\UserYii',
            'identityClass' => 'frontend\modules\oauth2\models\UserIdenty',
            'loginUrl' => ['site/login'],
            'enableAutoLogin' => false,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],

        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
          //  'traceLevel' => YII_DEBUG ? 3 : 0,
            'traceLevel' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'trace', 'info'],
                    'categories' => ['dbg'],
                    'logFile' => '@runtime/dbg/dbg.log',
                    'logVars' => [],
                ],
            ],
        ],
        /*
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        */
        'errorHandler' => [
            'errorAction'=>'v1/system/error',
            'class'=>'yii\web\ErrorHandler',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'i18n' => [
            'translations' => [
                'conquer/oauth2' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@conquer/oauth2/messages',
                ],
            ],
        ]
    ],
    'params' => $params,
];
