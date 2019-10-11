<?php

namespace common\components\conservation\models;

use backend\modules\adminx\models\UserM;
use Yii;

/**
 * This is the model class for table "conservation".
 *
 * @property integer $user_id
 * @property string $conservation
 *
 * @property UserM $user
 */
class Conservation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conservation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['conservation'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserM::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'conservation' => 'Conservation',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserM::className(), ['id' => 'user_id']);
    }



}
