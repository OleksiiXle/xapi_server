<?php

namespace backend\modules\adminx\controllers;

use common\components\conservation\ActiveDataProviderConserve;
use common\components\access\AccessControl;
use backend\modules\adminx\models\filters\UControlFilter;
use common\models\UControl;
use common\models\UserM;

/**
 * Class CheckController
 * Прпосмотр активности пользователей (зарегистрированных и гостей)
 * @package app\modules\adminx\controllers
 */
class CheckController extends MainController
{
    /**
     * @return array
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
                        'guest-control', 'user-control', 'delete-visitors', 'view-user', 'view-guest'
                    ],
                    'roles'      => ['systemAdminxx' ],
                ],
            ],
                /*
            'denyCallback' => function ($rule, $action) {
                \yii::$app->getSession()->addFlash("warning",\Yii::t('app', "Действие запрещено"));
                return $this->redirect(\Yii::$app->request->referrer);

        }
        */
        ];
        return $behaviors;
    }

    /**
     * @deprecated
     * @return string
     */
    public function actionUserControl()
    {
        $dataProvider = new ActiveDataProviderConserve([
            'filterModelClass' => UserActivityFilter::class,
            'conserveName' => 'userActivityGrid',
            'pageSize' => 20,
            'sort' => ['attributes' => [
                'user_id' => [
                    'asc' => [
                        'u_control.user_id' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.user_id' => SORT_DESC,
                    ],
                ],
                'remote_ip' => [
                    'asc' => [
                        'u_control.remote_ip' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.remote_ip' => SORT_DESC,
                    ],
                ],
                'username' => [
                    'asc' => [
                        'u_control.username' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.username' => SORT_DESC,
                    ],
                ],
                'createdAt' => [
                    'asc' => [
                        'u_control.created_at' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.created_at' => SORT_DESC,
                    ],
                ],
                'updatedAt' => [
                    'asc' => [
                        'u_control.updated_at' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.updated_at' => SORT_DESC,
                    ],
                ],
                'url' => [
                    'asc' => [
                        'u_control.url' => SORT_ASC,
                    ],
                    'desc' => [
                        'u_control.url' => SORT_DESC,
                    ],
                ],
            ]],

        ]);

        return $this->render('usersGrid',[
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionGuestControl()
    {
        $dataProvider = new ActiveDataProviderConserve([
            'filterModelClass' => UControlFilter::class,
            'conserveName' => 'guestActivityGrid',
            'pageSize' => 15,
            'sort' => ['attributes' => [
                'user_id' => [
                    'asc' => [
                        'uc.user_id' => SORT_ASC,
                    ],
                    'desc' => [
                        'uc.user_id' => SORT_DESC,
                    ],
                ],
                'remote_ip' => [
                    'asc' => [
                        'uc.remote_ip' => SORT_ASC,
                    ],
                    'desc' => [
                        'uc.remote_ip' => SORT_DESC,
                    ],
                ],
                'username' => [
                    'asc' => [
                        'user.username' => SORT_ASC,
                    ],
                    'desc' => [
                        'user.username' => SORT_DESC,
                    ],
                ],
                'createdAt' => [
                    'asc' => [
                        'uc.created_at' => SORT_ASC,
                    ],
                    'desc' => [
                        'uc.created_at' => SORT_DESC,
                    ],
                ],
                'updatedAt' => [
                    'asc' => [
                        'uc.updated_at' => SORT_ASC,
                    ],
                    'desc' => [
                        'uc.updated_at' => SORT_DESC,
                    ],
                ],
                'url' => [
                    'asc' => [
                        'uc.url' => SORT_ASC,
                    ],
                    'desc' => [
                        'uc.url' => SORT_DESC,
                    ],
                ],
            ]],

        ]);
        if (\Yii::$app->request->isPost){
            return $this->redirect('guest-control');
        }

        return $this->render('guestsGrid',[
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionDeleteVisitors()
    {
        if (\Yii::$app->request->isPost){
            $mode = \Yii::$app->request->get('mode');
            switch ($mode){
                case 'deleteAll':
                    $ret = UControl::deleteAll();
                    break;
                case 'deleteAllGuests':
                    $ret = UControl::deleteAll(['user_id' => 0]);
                    break;
                case 'deleteOldGuests':
                    $ret = UControl::clearOldRecords();
                    break;
            }
        }
        return $this->redirect('/adminx/check/guest-control');
    }

    /**
     * +++ Просмотр профиля пользователя
     * @return string
     */
    public function actionViewUser($id)
    {
        $user = UserM::findOne($id);
        $uControl = UControl::findOne(['user_id' => $id]);
        $userProfile = $user->userProfile;
        return $this->render('viewUser', [
            'userProfile' => $userProfile,
            'uControl' => $uControl,
        ]);
    }

    /**
     * +++ Просмотр профиля пользователя
     * @return string
     */
    public function actionViewGuest($ip)
    {
        $guest = UControl::findOne(['remote_ip' => $ip]);
        return $this->render('viewGuest', [
            'guest' => $guest,
        ]);
    }


}