<?php

namespace backend\modules\adminx\controllers;

use backend\modules\adminx\models\UserData;
use Yii;
use common\models\Functions;
use common\components\conservation\ActiveDataProviderConserve;
use common\components\conservation\models\Conservation;
use backend\modules\adminx\components\AccessControl;
use backend\modules\adminx\models\Assignment;
use backend\modules\adminx\models\filters\UserFilter;
use backend\modules\adminx\models\form\ChangePassword;
use backend\modules\adminx\models\form\ForgetPassword;
use backend\modules\adminx\models\form\Login;
use backend\modules\adminx\models\form\PasswordResetRequestForm;
use backend\modules\adminx\models\form\ResetPasswordForm;
use backend\modules\adminx\models\form\Signup;
use backend\modules\adminx\models\form\Update;
use backend\modules\adminx\models\UserM;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

/**
 * Class UserController
 * Управление пользователями
 * @package app\modules\adminx\controllers
 */
class UserController extends MainController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'forget-password', 'test', ],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'actions' => ['logout', 'test', 'change-password', 'update-profile', 'get-department-name'],
                    'roles' => ['@'],
                ],
                [
                    'allow'      => true,
                    'actions'    => [
                        'index', 'php-info', 'test' , 'conservation', 'view',
                    ],
                    'roles'      => ['systemAdminxx'],
                ],
                [
                    'allow'      => true,
                    'actions'    => [
                         'signup-by-admin', 'update-by-admin', 'delete', 'update-user-assignments', 'change-user-activity'
                    ],
                    'roles'      => ['systemAdminxx' ],
                ],
                [
                    'allow'      => true,
                    'actions'    => [
                        'php-info', 'export-to-exel', 'export-to-exel-prepare', 'upload-report', 'export-to-exel-count'
                        , 'export-to-exel-get-partition',
                    ],
                    'roles'      => ['systemAdminxx'],
                ],
                [
                    'allow'      => true,
                    'actions'    => [
                        'get-personal-data', 'get-personal-data-by-id', 'get-personal-data-by-fio',
                        'get-department-full-name', 'get-user-activity-info'
                    ],
                    'roles'      => ['systemAdminxx'],
                ],

            ],
            /*
            'denyCallback' => function ($rule, $action) {
            if (\Yii::$app->user->isGuest){
                $redirect = Url::to(\Yii::$app->user->loginUrl);
                return $this->redirect( $redirect);
            } else {
                \yii::$app->getSession()->addFlash("warning",\Yii::t('app', "Действие запрещено"));
                return $this->redirect(\Yii::$app->request->referrer);
            }
        }
            */
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['post'],
                'logout' => ['post'],
                'activate' => ['post'],
            ],

        ];
        return $behaviors;
    }

    /**
     * +++ Список всех пользователей
     * @return mixed
     */
  //  public function actionIndex($id = 564) {
    public function actionIndex()
    {
      //  $this->layout = '@app/modules/adminx/views/layouts/adminx.php';

        $dataProvider = new ActiveDataProviderConserve([
           // 'searchId' => $id,
            'filterModelClass' => UserFilter::class,
            'conserveName' => 'userAdminGrid',
            'pageSize' => 15,
            'sort' => ['attributes' => [
                'id',
                'username',
                'nameFam' => [
                    'asc' => [
                        'user_data.last_name' => SORT_ASC,
                    ],
                    'desc' => [
                        'user_data.last_name' => SORT_DESC,
                    ],
                ],
                'lastRoutTime' => [
                    'asc' => [
                        'user_data.last_rout_time' => SORT_ASC,
                    ],
                    'desc' => [
                        'user_data.last_rout_time' => SORT_DESC,
                    ],
                ],
                'lastRout' => [
                    'asc' => [
                        'user_data.last_rout' => SORT_ASC,
                    ],
                    'desc' => [
                        'user_data.last_rout' => SORT_DESC,
                    ],
                ],
                'status' => [
                    'asc' => [
                        'user.status' => SORT_ASC,
                    ],
                    'desc' => [
                        'user.status' => SORT_DESC,
                    ],
                ],
            ]],

        ]);
        $r=1;
        if (\Yii::$app->request->isPost){
            return $this->redirect('index');
        }
        return $this->render('index',[
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * +++ Login
     * @return string
     */
    public function actionLogin()
    {
     //   $this->layout = false;
    //    $this->layout = '@app/views/layouts/commonLayout.php';
        $model = new Login();
        if ($model->load(\Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     *+++ Logout
     * @return string
     */
    public function actionLogout(){
        \Yii::$app->getUser()->logout();
        //   return $this->goHome();
        return $this->redirect('/site/index');
    }

    /**
     * +++ Регистрация нового пользователя Администратором
     * @return string
     */
    public function actionSignupByAdmin()
    {
        $model = new UserM();
        $model->scenario = UserM::SCENARIO_CREATE;
        $defaultRoles = $model->defaultRoles;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->updateUser()) {
                $session = \Yii::$app->session;
                if ($session->get('searchIid')){
                    $session->remove('searchIid');
                }
                $session->set('searchIid', $model->id );

                return $this->redirect('index');
            }
        }

        return $this->render('updateUser', [
            'model' => $model,
            'defaultRoles' => $defaultRoles,
            'userDepartments' => [],
            'userRoles' => [],
        ]);
    }

    /**
     * +++ Редактирование профиля пользователя администратором
     * @return string
     */
    public function actionUpdateByAdmin($id)
    {
        $model = UserM::findOne($id);
        $model->scenario = UserM::SCENARIO_UPDATE;

        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($id);
        $userRoles = [];
        if (!empty($roles)){
            foreach ($roles as $key => $role){
                $userRoles[] = [
                    'id' => $key,
                    'name' => $role->description,
                ];
            }
        }
        $defaultRoles = $model->defaultRoles;

       // return $this->redirect('index');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->updateUser()) {
                return $this->redirect('index');
            }
        }

        return $this->render('updateUser', [
            'model' => $model,
            'userRoles' => $userRoles,
            'defaultRoles' => $defaultRoles,
        ]);
    }

    /**
     * +++ Просмотр профиля пользователя администратором
     * @return string
     */
    public function actionView($id)
    {
        $user = UserM::findOne($id);
        $userProfile = $user->userProfile;
        return $this->render('view', [
            'userProfile' => $userProfile,
        ]);
    }

    /**
     * +++ Редактирование разрешений и ролей пользователя администратором
     * @return string
     */
    public function actionUpdateUserAssignments($id)
    {
        $model = UserM::findOne($id);
        $ass = new Assignment($id);
        $assigments = $ass->getItemsXle();
        if (\Yii::$app->getRequest()->isPost) {
            $data = \Yii::$app->getRequest()->post('UserM');
            $ret = ($data['status'] == UserM::STATUS_INACTIVE) ? $model->deactivate() : $model->activate();
            if ($ret) {
                return $this->redirect('/adminx/user');
            }
        }

        return $this->render('updateUserAssignments', [
            'model' => $model,
            'user_id' => $id,
            'assigments' => $assigments,

        ]);
    }

    /**
     * Change password
     * @return string
     */
    public function actionChangePassword()
    {
        $model = new ChangePassword();
        if ($model->load(\Yii::$app->getRequest()->post()) && $model->change()) {
            return $this->goHome();
        }
        return $this->render('changePassword', [
            'model' => $model,
        ]);
    }

    /**
     * Set new password
     * @return string
     */
    public function actionForgetPassword()
    {

        $model = new ForgetPassword();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {// && $model->forgetPassword()
            $res = $model->forgetPassword();

            if($res===null){
                Yii::$app->getSession()->setFlash('userNotFound', 'User was not found.');
            }elseif($res){
                Yii::$app->getSession()->setFlash('newPwdSended', 'New password was sended.');
            }
        }

        return $this->render('forgetPassword', [
            'model' => $model,
        ]);
    }

    public function actionConservation($id)
    {
        $conservationJson = Conservation::find()
            ->where(['user_id' => $id])
            ->asArray()
            ->all();
        $conservation = ((isset($conservationJson[0]['conservation'])))
            ? json_decode($conservationJson[0]['conservation'], true)
            : [];
        return $this->render('conservation' , ['conservation' => $conservation]);
    }

    public function actionPhpInfo()
    {
        return $this->render('phpinfo');
    }









    /**
     * +++ Регистрация нового пользователя с подтверждением Емейла
     * @return string
     */
    public function actionSignup()
    {
        $model = new Signup();
        if (\Yii::$app->getRequest()->isPost) {
            $data = \Yii::$app->getRequest()->post('Signup');
            $model->setAttributes($data);
            $model->first_name = $data['first_name'];
            $model->middle_name =  $data['middle_name'];
            $model->last_name =  $data['last_name'];

            if ($user = $model->signup(true)) {
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'Check your email to confirm the registration'));
                return $this->goHome();
            } else {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'Ошибка отправки токена'));
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * +++ Подтверждение регистрации по токену
     * @return string
     */
    public function actionSignupConfirm($token)
    {
        $signupService = new Signup();

        try{
            $signupService->confirmation($token);
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Регистрация успешно подтверждена'));
        } catch (\Exception $e){
            \Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->goHome();
    }

    /**
     * +++ Редактирование профиля пользователя пользователем
     * @return string
     */
    public function actionUpdateProfile()
    {
        $id = \Yii::$app->user->getId();
        if (!empty($id)){
            $model = Update::findOne($id);
            $model->first_name = $model->userDatas->first_name;
            $model->middle_name = $model->userDatas->middle_name;
            $model->last_name = $model->userDatas->last_name;

            if (\Yii::$app->getRequest()->isPost) {
                $data = \Yii::$app->getRequest()->post('Update');
                $model->setAttributes($data);
                $model->first_name = $data['first_name'];
                $model->middle_name =  $data['middle_name'];
                $model->last_name =  $data['last_name'];

                if ($model->updateUser()) {
                    return $this->goHome();
                }
            }

            return $this->render('updateProfile', [
                'model' => $model,
                'user_id' => $id,

            ]);
        } else {
            \yii::$app->getSession()->addFlash("warning","Неверный ИД пользователя");
            return $this->redirect(\Yii::$app->request->referrer);

        }
    }

    /**
     * +++ Удаление профиля пользователя
     * @return string
     */
    public function actionDelete($id)
    {
        if (\Yii::$app->request->isPost){
            $userDel = UserM::findOne($id)->delete();
            if ($userDel === 0){
                \yii::$app->getSession()->addFlash("warning","Ошибка при удалении.");
            }
        }
        return $this->redirect('index');

    }



    /**
     * Запрос на смену пароля через Емейл
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                \Yii::$app->session->setFlash('success',
                    \Yii::t('app', 'На Ваш електронный адрес отправлено письмо для изменения пароля'));
            } else {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'Не удалось сбросить пароль с помощью Email'));
            }
            return $this->goHome();
        }

        return $this->render('passwordResetRequest', [
            'model' => $model,
        ]);
    }

    /**
     * Смена пароля по токену из Емейла
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $model = new ResetPasswordForm($token);

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Новый пароль сохранен'));
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,]);
      }



    public function actionTest()
    {
        $this->layout = '@app/modules/adminx/views/layouts/testLayout.php';
      //  $this->layout = false;
        $t = 1;
        return $this->render('test');
    }


    //******************** АЯКС


    public function actionChangeUserActivity()
    {
        $response['status'] = false;
        $response['data'] = ['Данні не знайдено'];
        $_post    = \yii::$app->request->post();
        if (isset($_post['user_id'])) {
            $user = UserM::findOne($_post['user_id']);
            if (isset($user)){
                switch ($user->status){
                    case UserM::STATUS_ACTIVE:
                        $ret = $user->deactivate();
                        $response['data'] = 'inactive';
                        break;
                    case UserM::STATUS_INACTIVE:
                        $ret = $user->activate();
                        $response['data'] = 'active';
                        break;
                    default:
                        $response['data'] = 'Невірний статус';
                        return json_encode($response);
                }
                if (!$ret){
                    $response['data'] = $user->showErrors();
                } else {
                    $response['status'] = true;
                }
            }
        }
        return json_encode($response);

    }

    public function actionGetPersonalDataByFio(){
        /*
                    'last_name': last_name,
            'first_name': first_name,
            'middle_name': middle_name

         */
        // 0082166
        $response['status'] = false;
        $response['data'] = ['Данні не знайдено'];
        $_post    = \yii::$app->request->post();
        if (isset($_post['last_name']) && !empty($_post['last_name'])){
            $last_name = $_post['last_name'];
        }
        if (isset($last_name)){
           // $query = PersonalCommon::find()
            $query = (new Query())
                ->select('id, name_family, name_first, name_last, ')
                ->from('personal')
                ->where(['name_family' => $last_name]);
            if (isset($_post['first_name']) && !empty($_post['first_name'])){
                $first_name = $_post['first_name'];
                $query->andWhere(['name_first' => $first_name]);
            }
            if (isset($_post['middle_name']) && !empty($_post['middle_name'])){
                $middle_name= $_post['middle_name'];
                $query->andWhere(['name_last' => $middle_name]);
            }
            $rr = 1;
            $personal = $query
                ->orderBy('name_first')
                ->all();
            if (!empty($personal)){
                $response['data'] = [];
           //    foreach ($personal as $persona){
                for ($i = 0; $i < count($personal); $i++){
                    $persona = PersonalCommon::find()
                    ->where(['id' => $personal[$i]['id']])
                    ->one();
                    $personal_id = $persona->id;
                    $positionFullName =$persona->positionCommon->revertSemiFullName;;
                    $response['data'][] =
                        [
                            'id' => $personal_id,
                            'name' => $persona->name_family
                                . ' ' . $persona->name_first . ' ' . $persona->name_last
                                . ' *** ' . $positionFullName . ' *** ' ]
                        ;

                }
                $response['status'] = true;
            }
        }
        return json_encode($response);

    }

    public function actionGetDepartmentFullName(){
        $response['status'] = false;
        $response['data'] = ['Данні не знайдено'];
        $_post    = \yii::$app->request->post();
        if (isset($_post['department_id'])){
            $department_id = $_post['department_id'];
        }
        if (isset($department_id)){
            $department = DepartmentCommon::findOne($department_id);
            if (isset($department)){
                $response['status'] = true;
                $response['data'] = $department->getFullNameRevert(DepartmentCommon::ROOT_ID);
            }
        }
        return json_encode($response);

    }




    /**
     * Вывод в EXEL данных
     * @return string
     */
    public function actionExportToExel()
    {
        $_get = \Yii::$app->request->get();
        $_post = \Yii::$app->request->post();
        if (isset($_get['exportQuery'])){
            $exportQuery = $_get['exportQuery'];
        } elseif (isset($_post['exportQuery'])){
            $exportQuery = $_post['exportQuery'];
        } else {
            $exportQuery = [];
        }
        if (!empty($exportQuery)){
            $query = new $exportQuery['filterModelClass'];
            if (!empty($exportQuery['filter'])){
                $query->setAttributes($exportQuery['filter']);
            }
            $ret = $query->getQuery();
            if (!empty($exportQuery['sort'])){
                $ret->addOrderBy($exportQuery['sort']);
            }
            $users = $ret->all();
            if (!empty($users)){
                foreach ($users as $user){

                    $result[]= $user->userProfileStrShort;
                }
                $pathToFile = \Yii::getAlias('@app/web/tmp');
                $userId = \Yii::$app->user->getId();
                Functions::exportToExel($result, $pathToFile, $userId, 'report_' );
                return true;
            }
        }
        return $this->redirect('index');
    }

    /**
     * Вывод в EXEL данных AJAX (подготовка временного файла)
     * @return string
     */
    public function actionExportToExelPrepare()
    {
        ini_set("memory_limit", "512M");
        try{
            $_post = \Yii::$app->request->post();
            if (isset($_post['exportQuery'])){
                $exportQuery = [
                    'filterModelClass' => $_post['exportQuery']['filterModelClass'],
                    'filter' => json_decode($_post['exportQuery']['filter'], true),
                    'sort' => json_decode($_post['exportQuery']['sort'], true),
                ];
                $query = new $exportQuery['filterModelClass'];
                if (!empty($exportQuery['filter'])){
                    $query->setAttributes($exportQuery['filter']);
                }
                $ret = $query->getQuery();
                if (!empty($exportQuery['sort'])){
                    $ret->addOrderBy($exportQuery['sort']);
                }
                $users = $ret->all();
                if (!empty($users)){
                    foreach ($users as $user){

                        $result[]= $user->userProfileStrShort;
                    }
                    $pathToFile = \Yii::getAlias('@app/web/tmp');
                    $userId = \Yii::$app->user->getId();
                    $this->result = Functions::exportToExel($result, $pathToFile, $userId, 'report_', 'Список', false );
                }
            }
        } catch (\Exception $e){
            $this->result['data'] = $e->getMessage();
        }
        return json_encode($this->result);
    }

    public function actionUploadReport(){
        $userId = \Yii::$app->user->getId();

        $pathToFile = \Yii::getAlias('@app/web/tmp/report_') . $userId . '.xls';

        $ret = Functions::uploadFileXle($pathToFile,true);
        return $ret;
    }




    /**
     * Вывод в EXEL данных AJAX (определение количества записей)
     * @return string
     */
    public function actionExportToExelCount()
    {
        try{
            $_post = \Yii::$app->request->post();
            if (isset($_post['exportQuery'])){
                $userId = \Yii::$app->user->getId();
                $fileFullName = \Yii::getAlias('@app/web/tmp/report_') . $userId . '.xls';

                if (file_exists($fileFullName)){
                    unlink($fileFullName);
                }
                $exportQuery = [
                    'filterModelClass' => $_post['exportQuery']['filterModelClass'],
                    'filter' => json_decode($_post['exportQuery']['filter'], true),
                    'sort' => json_decode($_post['exportQuery']['sort'], true),
                ];
                $query = new $exportQuery['filterModelClass'];
                if (!empty($exportQuery['filter'])){
                    $query->setAttributes($exportQuery['filter']);
                }
                $ret = $query->getQuery();
                $this->result['data'] = $ret->count();
                $this->result['status'] = true;
            }
        } catch (\Exception $e){
            $this->result['data'] = $e->getMessage();
        }
        return json_encode($this->result);
    }

    /**
     * Вывод в EXEL данных AJAX (определение количества записей)
     * @return string
     */
    public function actionExportToExelGetPartition()
    {
        try{
            $_post = \Yii::$app->request->post();
            if (isset($_post['exportQuery']) && isset($_post['limit']) && isset($_post['offset'])){
                $userId = \Yii::$app->user->getId();
                $fileFullName = \Yii::getAlias('@app/web/tmp/report_') . $userId . '.xls';


                $exportQuery = [
                    'filterModelClass' => $_post['exportQuery']['filterModelClass'],
                    'filter' => json_decode($_post['exportQuery']['filter'], true),
                    'sort' => json_decode($_post['exportQuery']['sort'], true),
                ];
                $query = new $exportQuery['filterModelClass'];
                if (!empty($exportQuery['filter'])){
                    $query->setAttributes($exportQuery['filter']);
                }
                $ret = $query->getQuery();
                if (!empty($exportQuery['sort'])){
                    $ret->addOrderBy($exportQuery['sort']);
                }
                $users = $ret
                    ->limit($_post['limit'])
                    ->offset($_post['offset'])
                    ->all();
                if (!empty($users)){
                    foreach ($users as $user){

                        $result[]= $user->userProfileStrShort;
                    }
                    $this->result = Functions::exportToExelUniversal($result, $fileFullName,  'Список', false );
                }
            }
        } catch (\Exception $e){
            $this->result['data'] = $e->getMessage();
        }
        return json_encode($this->result);
    }





}