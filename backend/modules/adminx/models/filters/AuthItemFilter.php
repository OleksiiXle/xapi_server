<?php
namespace backend\modules\adminx\models\filters;

use backend\modules\adminx\models\AuthItem;
use yii\base\Model;

class AuthItemFilter extends Model
{
    public $name;
    public $type;
    public $description;
    public $rule_name;



    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'integer'],
            [['description', 'rule_name' , 'name'], 'string', 'max' => 64]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => \Yii::t('app', 'Тип'),
            'name' => \Yii::t('app', 'Название'),
            'rule_name' => \Yii::t('app', 'Правило'),
            'description' => \Yii::t('app', 'Описание'),
        ];
    }


    public function getQuery($params = null){
        switch ($this->type){
            case AuthItem::TYPE_All:
                $query = AuthItem::find();
                break;
            case AuthItem::TYPE_ROLE:
                $query = AuthItem::find()
                    ->andWhere(['type' => AuthItem::TYPE_ROLE]);
                break;
            case AuthItem::TYPE_PERMISSION:
                $query = AuthItem::find()
                    ->andWhere(['type' => AuthItem::TYPE_PERMISSION])
                    ->andWhere('NOT (name LIKE "/%")');
                break;
            case AuthItem::TYPE_ROUTE:
                $query = AuthItem::find()
                    ->andWhere(['type' => AuthItem::TYPE_PERMISSION])
                    ->andWhere('name LIKE "/%"');
                break;
            default:
                $query = AuthItem::find();

        }


        if (!$this->validate()) {
            return $query;
        }

        if (!empty($this->name)) {
            $query->andWhere(['like', 'name', $this->name]);
        }

        if (!empty($this->rule_name) && $this->rule_name != \Yii::t('app', 'Без правила')) {
            $query->andWhere(['like', 'rule_name', $this->rule_name]);
        }


        return $query;







    }



}