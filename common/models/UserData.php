<?php

namespace common\models;

use Yii;
use common\models\Functions;
use common\models\MainModel;

/**
 * This is the model class for table "user_data".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 *
 * @property User $user
 */
class UserData extends MainModel
{
    private $_userLogin;
    private $_userFio;

    public $activityInterval = 3600;

    public static $activityIntervalArray=[
        0 => 'Увесь час',
        3600 => '1 година',
        7200 => '2 години',
        10800 => '3 години',
        86400 => '1 доби',
        172800 => '2 доби',
        259200 => '3 доби',
        345600 => '4 доби',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'first_name', 'middle_name', 'last_name'], 'required'],
            [['user_id', ], 'integer'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
          //  [['spec_document'], 'exist', 'skipOnError' => true, 'targetClass' => PersonalCommon::className(),
          //      'targetAttribute' => ['spec_document' => 'spec_document']],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ІД',
            'email' => 'Email',
            'first_name' => 'Імя',
            'middle_name' => 'По батькові',
            'last_name' => 'Прізвище',
            'userLogin' => 'Логін',
            'userFio' => 'П.І.Б.',
        ];
    }


//*********************************************************************************************** ДАННЫЕ СВЯЗАННЫХ ТАБЛИЦ
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUserM()
    {
        return $this->hasOne(UserM::class, ['id' => 'user_id']);
    }

//*********************************************************************************************** ГЕТТЕРЫ-СЕТТЕРЫ
    public function getUserFio()
    {
        $this->_userFio = $this->last_name . ' ' . mb_substr($this->first_name,0,1) . '.'
            . mb_substr($this->middle_name,0,1) . '.';
        return $this->_userFio;
    }

    public function getUserLogin()
    {
        $this->_userLogin = $this->user->username;
        return $this->_userLogin;
    }

//*********************************************************************************************** ФУНКЦИИ
}
