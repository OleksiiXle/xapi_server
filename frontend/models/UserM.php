<?php

namespace frontend\models;

use Yii;
use common\models\Functions;
use common\models\MainModel;
use yii\db\Query;

/**
 * User - модель с правилами, геттерами, сеттерами и пр. данными
 *
 */
class UserM extends MainModel
{
    const STATUS_INACTIVE = 0;
    const STATUS_WAIT = 5;
    const STATUS_ACTIVE = 10;

    const STATUS_DICT = [
      self::STATUS_INACTIVE => 'Не активний',
      self::STATUS_WAIT => 'Очікує на підтвердження',
      self::STATUS_ACTIVE => 'Активний',
    ];

    const SCENARIO_CREATE  = 'create';
    const SCENARIO_UPDATE  = 'update';
    const SCENARIO_ACTIVATE  = 'activate';
    const SCENARIO_DEACTIVATE  = 'deactivate';
    const SCENARIO_RESET_PASSWORD  = 'resetPassword';


    const USER_NAME_PATTERN           = '/^[А-ЯІЇЄҐ]{1}[а-яіїєґ\']+([-]?[А-ЯІЇЄҐ]{1}[а-яіїєґ\']+)?$/u'; //--маска для нимени
    const USER_NAME_ERROR_MESSAGE     = 'Використовуйте українські літери, починаючи із великої. 
                                         Апостроф - в англійській розкладці на букві є. Подвійні імена - через тире!'; //--сообщение об ошибке
    const USER_PASSWORD_PATTERN       = '/^[a-zA-Z0-9_]+$/ui'; //--маска для пароля
    const USER_PASSWORD_ERROR_MESSAGE = 'Припустимі символи - латиниця та цифри'; //--сообщение об ошибке
    const USER_PHONE_PATTERN       = '/^[0-9, \-)(+]+$/ui'; //--маска для пароля
    const USER_PHONE_ERROR_MESSAGE = 'Припустимі символи - латиниця та цифри'; //--сообщение об ошибке

    const JOB_PATTERN = '/^[А-ЯІЇЄҐа-яіїєґ0-9A-Z ().№ʼ,«»\'"\-]+$/u'; //--маска для нимени
    const JOB_ERROR_MESSAGE = 'Використовуйте українські та великі латинські літери, цифри, пробел, лапки, символи ( . , № ʼ \'  " « »  - )'; //--сообщение об ошибке


    public $first_name;
    public $middle_name;
    public $last_name;

    public $password;
    public $retypePassword;
    public $oldPassword;
    public $newPassword;
    public $rememberMe = true;

    public $multyFild;  //TODO прописать валидацию

    private $_user = false;
    private $_created_at_str;
    private $_updated_at_str;
    private $_userRoles;
    private $_nameFam; //-- фамилия
    private $_nameNam;//--имя
    private $_nameFat;//-- отчество

    private $_userProfile;
    private $_userProfileStrFull;
    private $_userProfileStrShort;
    private $_userCreater;
    private $_userUpdater;
    private $_defaultRoles;

    private $_firstVisitTime;
    private $_lastVisitTime;
    private $_firstVisitTimeTxt;
    private $_lastVisitTimeTxt;
    private $_lastRoute;


    public static function tableName()
    {
        return 'user';
    }

    public function scenarios()
    {
        $ret = parent::scenarios();
        $ret[self::SCENARIO_CREATE] = [
            'username' , 'email', 'first_name', 'middle_name', 'last_name', 'password', 'retypePassword',
            'status', 'multyFild'
        ];
        $ret[self::SCENARIO_UPDATE] = [
            'email', 'first_name', 'middle_name', 'last_name', 'multyFild'

        ];
        $ret[self::SCENARIO_ACTIVATE] = [
            'status'
        ];
        $ret[self::SCENARIO_DEACTIVATE] = [
            'status'
        ];
        $ret[self::SCENARIO_RESET_PASSWORD] = [
        ];
        return $ret ;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'trim'],
            //----------------------------------------------------------------------- ТИПЫ ДАННЫХ, РАЗМЕР
            [['status', 'created_at', 'updated_at','created_by', 'updated_by', ], 'integer'],

            ['rememberMe', 'boolean'],
            ['email', 'email'],

            [['multyFild' ], 'string'],
            [['username', 'auth_key'], 'string', 'min' => 4, 'max' => 32],
            [['password', 'retypePassword', 'oldPassword' , 'newPassword'],  'string', 'min' => 3, 'max' => 20],
            [['first_name', 'middle_name', 'last_name',
                'password_hash', 'password_reset_token', 'email' ], 'string', 'max' => 255],

            //------------------------------------------------------------------------ УНИКАЛЬНОСТЬ
        //    ['username', 'unique', 'targetClass' => 'app\modules\adminx\models\User'],
       //     ['email', 'unique', 'targetClass' => 'app\modules\adminx\models\User'],
            ['username', 'validateUsername'],
            ['email', 'validateEmail'],

            //------------------------------------------------------------------------ МАСКИ ВВОДА
            [['username', 'password', 'oldPassword', 'retypePassword',  'newPassword' ], 'match', 'pattern' => self::USER_PASSWORD_PATTERN,
                'message' => self::USER_PASSWORD_ERROR_MESSAGE],
            [['first_name', 'middle_name', 'last_name'],  'match', 'pattern' => self::USER_NAME_PATTERN,
                'message' => self::USER_NAME_ERROR_MESSAGE],

            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],

            //-------------------------------------------------------------------------------------- ОБЯЗАТЕЛЬНЫЕ ПОЛЯ
            [['username' , 'email', 'first_name', 'middle_name', 'last_name', 'password', 'retypePassword'], 'required',
                'on' => self::SCENARIO_CREATE],
            [['first_name', 'middle_name', 'last_name'], 'required',
                'on' => self::SCENARIO_UPDATE],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            //-- user
            'id' => 'ID',
            'username' => 'Логін',
            'auth_key' => 'Ключ авторізації',
            'password_hash' => 'Пароль',
            'password_reset_token' => 'Токен збросу паролю',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Створений',
            'updated_at' => 'Змінений',
            'refresh_permissions' => 'Потрібне оновлення дозвілів',

            //-- user_data
            'first_name' => 'Імя',
            'middle_name' => 'По батькові',
            'last_name' => 'Прізвище',

            //---- служебные
            'password' => 'Пароль',
            'oldPassword' => 'Старий пароль',
            'retypePassword' => 'Підтвердждення паролю',
            'departments' => 'Дозволені підрозділи',

            //----  геттеры
            'created_at_str' => 'Створений',
            'updated_at_str' => 'Змінений',

            //-- устаревшие
            'lastRoutTime' => 'Остання активність',
            'lastRout' => 'Останній роут',
            'nameFam' => 'Прізвище',
            'nameNam' => 'Імя',
            'nameFat' => 'По батькові',
            'userRoles' => 'Ролі',

        ];
    }

    public function validateEmail()
    {
        $checkCondition = ($this->isNewRecord) ? ['email' => $this->email] : 'email = "' . $this->email . '" AND id != ' . $this->id;
        $check = self::find()->where($checkCondition)->count();
        if (!empty($check)) {
            $this->addError('email', 'Email вже зайнято');
        }
    }

    public function validateUsername()
    {
        $checkCondition = ($this->isNewRecord) ? ['username' => $this->username] : 'username = ' . $this->username . ' AND id != ' . $this->id;
        $check = self::find()->where($checkCondition)->count();
        if (!empty($check)) {
            $this->addError('username', 'username вже зайнято');
        }
    }



//*********************************************************************************************** ДАННЫЕ СВЯЗАННЫХ ТАБЛИЦ

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDatas()
    {
        return $this->hasOne(UserData::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUControl()
    {
        return $this->hasOne(UControl::className(), ['user_id' => 'id']);
    }

//*********************************************************************************************** ГЕТТЕРЫ-СЕТТЕРЫ

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function getNameFam()
    {
        $this->_nameFam = (isset($this->userDatas->last_name)) ? $this->userDatas->last_name : '';
        return $this->_nameFam;
    }

    public function getNameNam()
    {
        $this->_nameNam = (isset($this->userDatas->first_name)) ? $this->userDatas->first_name : '';
        return $this->_nameNam;
    }

    public function getNameFat()
    {
        $this->_nameFat = (isset($this->userDatas->middle_name)) ? $this->userDatas->middle_name : '';
        return $this->_nameFat;
    }

    public function getCreated_at_str()
    {
        $this->_created_at_str = Functions::intToDateTime($this->created_at);
        return $this->_created_at_str;
    }

    public function getUpdated_at_str()
    {
        $this->_updated_at_str = Functions::intToDateTime($this->updated_at);
        return $this->_updated_at_str;
    }

    public function getUserRoles()
    {
        $this->_userRoles = '';
        $roles = \Yii::$app->authManager->getRolesByUser($this->id);
        if (isset($roles)){
            foreach ($roles as $role){
                $this->_userRoles .= $role->name . ' ';
            }
        }
        return $this->_userRoles;
    }

    public static function getStatusDict(){

        return [
            self::STATUS_INACTIVE => 'Не активний',
            self::STATUS_WAIT => \Yii::t('app', 'Очікує на підтвердження'),
            self::STATUS_ACTIVE => 'Активний',
        ];
    }

    /**
     * @return mixed
     */
    public function getDefaultRoles()
    {

        $this->_defaultRoles = Yii::$app->params['defaultRoles'];
        return $this->_defaultRoles;
    }

    public function getUserCreater()
    {
        $this->_userCreater = '';
        $user = self::findOne(['id' => $this->created_by]);
        if (isset($user)){
            $this->_userCreater = $user->username;
        }
        return $this->_userCreater;
    }

    public function getUserUpdater()
    {
        $this->_userUpdater = '';
        $user = self::findOne(['id' => $this->updated_by]);
        if (isset($user)){
            $this->_userUpdater = $user->username;
        }
        return $this->_userUpdater;
    }

    public function getUserProfile()
    {
        $userDatas = $this->userDatas;
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);
        $permissions = $auth->getPermissionsByUser($this->id);
        $userRoles = [];
        $userPermissions = [];
        $userRoutes = [];
        if (!empty($roles)){
            foreach ($roles as $key => $role){
                $userRoles[] = [
                    'id' => $key,
                    'name' => $role->description,
                ];
            }
        }
        if (!empty($permissions)){
            foreach ($permissions as $key => $permission){
                if (substr($key, 0, 1) == '/'){
                    $userRoutes [] = [
                        'id' => $key,
                        'name' => $role->description,
                    ];
                } else {
                    $userPermissions [] = [
                        'id' => $key,
                        'name' => $role->description,
                    ];
                }
            }
        }

        //---------------------
        $this->_userProfile = [
            'id' => $this->id,
            'status' => $this->status,
            'username' => $this->username,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'userRoles' => $userRoles,
            'userPermissions' => $userPermissions,
            'userRoutes' => $userRoutes,

            'created_at' => ($this->created_at)
                ? Functions::intToDateTime($this->created_at) : Functions::intToDateTime($this->created_at),
            'updated_at' => ($this->updated_at)
                ? Functions::intToDateTime($this->updated_at) : Functions::intToDateTime($this->updated_at),
            'userUpdater' => $this->userUpdater,
            'userCreater' => $this->userCreater,
            //-- u_control
            'firstVisitTimeTxt' => $this->firstVisitTimeTxt,
            'lastVisitTimeTxt' => $this->lastVisitTimeTxt,
            'lastRoute' => $this->lastRoute,
        ];

        return $this->_userProfile;
    }

    /**
     * @return mixed
     */
    public function getUserProfileStrFull()
    {
        $userDatas = $this->userDatas;
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);
        $permissions = $auth->getPermissionsByUser($this->id);
        $userRoles = '';
        $userPermissions = '';
        $userRoutes = '';

        $endStr = PHP_EOL;
        if (!empty($roles)){
            foreach ($roles as $key => $role){
                $userRoles .= $key . ' - ' . $role->description . $endStr;
                if (!empty($endStr)){
                    $endStr = '';
                }
            }
        }

        $endStr = PHP_EOL;
        $endStr1 = PHP_EOL;
        if (!empty($permissions)){
            foreach ($permissions as $key => $permission){
                if (substr($key, 0, 1) == '/'){
                    $userRoutes .= $key . ' - ' . $permission->description . $endStr;
                    if (!empty($endStr)){
                        $endStr = '';
                    }
                } else {
                    $userPermissions .= $key . ' - ' . $permission->description . $endStr1;
                    if (!empty($endStr1)){
                        $endStr1 = '';
                    }
                }
            }
        }

        $userDepartments = $this->userDepartmentsData;
        $userDepartmentsStr = '';
        $endStr = PHP_EOL;
        if (!empty($userDepartments)){
            foreach ($userDepartments as $department){
                $userDepartmentsStr .=  $department['name'] . $endStr;
                if (!empty($endStr)){
                    $endStr = '';
                }
            }
        }

        //---------------------
        $this->_userProfileStrFull = [
            'id' => $this->id,
            'status' => self::STATUS_DICT[$this->status],
            'username' => $this->username,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'userRoles' => $userRoles,
            'userPermissions' => $userPermissions,
            'userRoutes' => $userRoutes,
            'created_at' => ($this->created_at)
                ? Functions::intToDateTime($this->created_at) : Functions::intToDateTime($this->created_at),
            'updated_at' => ($this->updated_at)
                ? Functions::intToDateTime($this->updated_at) : Functions::intToDateTime($this->updated_at),
            'userUpdater' => $this->userUpdater,
            'userCreater' => $this->userCreater,
            'firstVisitTimeTxt' => $this->firstVisitTimeTxt,
            'lastVisitTimeTxt' => $this->lastVisitTimeTxt,
            'lastRoute' => $this->lastRoute,
        ];

        return $this->_userProfileStrFull;
    }

    /**
     * @return mixed
     */
    public function getUserProfileStrShort()
    {
        $userProfileStrFull = $this->userProfileStrFull;

        $this->_userProfileStrShort = [
            'status' => $userProfileStrFull['status'],
            'username' => $userProfileStrFull['username'],
            'last_name' => $userProfileStrFull['last_name'],
            'first_name' => $userProfileStrFull['first_name'],
            'middle_name' => $userProfileStrFull['middle_name'],
            'email' => $userProfileStrFull['email'],
            'userRoles' => $userProfileStrFull['userRoles'],
        ];

        return $this->_userProfileStrShort;
    }

    /**
     * @return mixed
     */
    public function getFirstVisitTime()
    {
        $this->_firstVisitTime = 0;
        if (isset($this->uControl)){
            $this->_firstVisitTime = $this->uControl->created_at;
        }
        return $this->_firstVisitTime;
    }

    /**
     * @return mixed
     */
    public function getLastVisitTime()
    {
        $this->_lastVisitTime = 0;
        if (isset($this->uControl)){
            $this->_lastVisitTime = $this->uControl->updated_at;
        }
        return $this->_lastVisitTime;
    }

    /**
     * @return mixed
     */
    public function getFirstVisitTimeTxt()
    {
        $this->_firstVisitTimeTxt = '';
        if (isset($this->uControl)){
            $this->_firstVisitTimeTxt = Functions::intToDateTime($this->uControl->created_at);
        }
        return $this->_firstVisitTimeTxt;
    }

    /**
     * @return mixed
     */
    public function getLastVisitTimeTxt()
    {
        $this->_lastVisitTimeTxt = '';
        if (isset($this->uControl)){
            $this->_lastVisitTimeTxt = Functions::intToDateTime($this->uControl->updated_at);
        }
        return $this->_lastVisitTimeTxt;
    }

    /**
     * @return mixed
     */
    public function getLastRoute()
    {
        $this->_lastRoute = '';
        if (isset($this->uControl)){
            $this->_lastRoute = $this->uControl->url;
        }
        return $this->_lastRoute;
    }


//*********************************************************************************************** CRUD

    /**
     * @return boolean
     */
    public function updateUser()
    {
        $create = $this->isNewRecord;
        $multyFild =json_decode($this->multyFild, true);
        //    return false;
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            //-- создание пользователя
            if ($create){
                $this->setPassword($this->password);
                $this->generateAuthKey();
                $this->status = UserM::STATUS_ACTIVE;
            }
            if ($this->save()){
                if ($create){
                    $userData = new UserData();
                    $userData->setAttributes((array) $this);
                    $userData->user_id = $this->id;
                } else {
                    $userData = UserData::find()
                        ->where(['user_id' => $this->id])->one();
                    $userData->setAttributes((array) $this);
                }
                if ($userData->save()){
                    //-------------------------------- назначение роли
                    $auth = Yii::$app->authManager;
                    $userRolesOld = $auth->getRolesByUser($this->id);
                    if (!$create){
                        //--- сброс всех ролей, помеченных, как дефолтные
                        $defaultRoles = $this->defaultRoles;
                        foreach ($userRolesOld as $name => $role){
                            if (isset($defaultRoles[$name])){
                                $ret = $auth->revoke($role,$this->id);
                            }
                        }
                    }

                    //-- добавление ролей
                    $userRoles = $multyFild['roles'];
                    foreach ($userRoles as $role){
                        $userRole = $auth->getRole($role['id']);
                        if (isset($userRole)){
                            $ret = $auth->assign($userRole, $this->id);
                            if (!$ret){
                                $this->addError('id', "Помилка призначення ролі " . $role['id']);
                                $transaction->rollBack();
                                return false;
                            }
                        } else {
                            $this->addError('id', "Роль " . $role['id'] . " не знайдена");
                            $transaction->rollBack();
                            return false;
                        }
                    }
                    //------------------------------- все ок
                    $transaction->commit();
                    return true;
                } else {
                    $this->addErrors($userData->getErrors());
                }
            }
            $transaction->rollBack();
            return false;

        } catch (\Exception $e){
            if (isset($transaction) && $transaction->isActive) {
                $transaction->rollBack();
            }
            $this->addError('id', $e->getMessage());
            return false;

        }
    }

    public function activate()
    {
        $this->scenario = self::SCENARIO_ACTIVATE;
        $this->status = UserM::STATUS_ACTIVE;
        return $this->save();
    }

    public function deactivate()
    {
        $this->scenario = self::SCENARIO_DEACTIVATE;
        $this->status = UserM::STATUS_INACTIVE;
        return $this->save();
    }


//********************************************************************************* МЕДОТЫ АВТОРИЗАЦИИ И АУТЕНТИФИКАЦИИ
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, \Yii::t('app', 'Невірний логін або пароль'));
            } elseif ($user->status != self::STATUS_ACTIVE) {
                $this->addError($attribute, 'Ваш статус - ' . self::STATUS_DICT[$user->status]);
            }
        }
    }

    public function validateOldPassword()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;
        if (!$user || !$user->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', \Yii::t('app', 'Неверный старый пароль'));
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

//********************************************************************************** ПЕРЕОПРЕДЕЛЕННЫЕ МЕТОДЫ

    public function beforeSave($insert) {
        $this->updated_at = time();
        $this->updated_by = (isset(\Yii::$app->user->id)) ? \Yii::$app->user->id : 0;
        if ($insert){
            $this->created_at = time();
            $this->created_by = (isset(\Yii::$app->user->id)) ? \Yii::$app->user->id : 0;
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        if (isset($this->userDatas)){
            $data = $this->userDatas;
            $this->first_name = $data->first_name;
            $this->middle_name = $data->middle_name;
            $this->last_name = $data->last_name;
        }
        parent::afterFind(); // TODO: Change the autogenerated stub
    }



//********************************************************************************** FOR RESET PASSWORD

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * +++ Finds user for reset password
     *
     * @param string $data
     * @return static|null
     */
    public static function findForReset($data)
    {
        //return var_dump(stripos($data, '@'));
        if(stripos($data, '@')){
            return static::findByEmail(trim($data));
        }else{
            return static::findByUsername(trim($data));
        }
    }

    /**
     * Set new Password for user.
     *
     * @return User|false the saved model or null if saving fails
     */
    public function resetPassword() {
        $this->setPassword($this->newPassword);
        $this->generateAuthKey();
        if ($this->save(false)) {
            return true;
        }
        return false;
    }

//********************************************************************************** Другие функции

}
