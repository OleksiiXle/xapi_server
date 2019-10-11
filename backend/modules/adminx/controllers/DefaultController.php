<?php
namespace backend\modules\adminx\controllers;

use Yii;
use backend\modules\adminx\components\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class AssignmentController
 * Управление разрешениями пользователя
 * @package app\modules\adminx\controllers
 */
class DefaultController extends MainController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'      => true,
                    'actions'    => [
                        'index',
                    ],
                    'roles'      => ['systemAdminxx', ],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $userPermissions = Yii::$app->authManager->userRolesPermissions;
        $buttons = [
            'users' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Користувачі',
                    'route' => '/adminx/user'
                ]
                : [
                    'show' => false,
                ],
            'rules' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Правила',
                    'route' => '/adminx/rule'
                ]
                : [
                    'show' => false,
                ],
            'authItems' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Дозвіли, ролі',
                    'route' => '/adminx/auth-item'
                ]
                : [
                    'show' => false,
                ],
            'menuEdit' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Редактор меню',
                    'route' => '/adminx/menux/menu'
                ]
                : [
                    'show' => false,
                ],
            'configs' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Системні налаштування',
                    'route' => '/adminx/configs/update'
                ]
                : [
                    'show' => false,
                ],
            'guestControl' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'Відвідування сайту',
                    'route' => '/adminx/check/guest-control'
                ]
                : [
                    'show' => false,
                ],
            'PHPinfo' => (isset($userPermissions['systemAdminxx']))
                ? [
                    'show' => true,
                    'name' => 'PHP-info',
                    'route' => 'adminx/user/php-info'
                ]
                : [
                    'show' => false,
                ],

        ];
        $r=1;
        return $this->render('index',
            [
                'buttons' => $buttons,
            ]);
    }

}
