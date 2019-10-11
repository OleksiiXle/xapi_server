<?php

namespace backend\modules\kino\models;

use common\models\MainModel;
use Yii;

class Kino extends MainModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kino';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'cinema_hall'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['cinema_hall'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'cinema_hall' => 'Кино-зал',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function saveHall()
    {
        $i = 1;
        $t = json_decode($this->cinema_hall, true);
        $ret = [];
        foreach ($t as $seats){
           // $seats = json_decode($row, true);
            $buf = [];
            foreach ($seats as $data){
                $arr = json_decode($data, true);
                $buf[$arr['number']] = [
                    'number' => $arr['number'],
                    'status' => $arr['status'],
                    'price' => $arr['price'],
                    'persona' => $arr['persona'],
                ];
            }
            $ret[$i++] = $buf;
        }
        $this->cinema_hall = json_encode($ret);
        return $this->save();

    }

    public static function hallsList()
    {
        $ret = [];
        $halls = self::find()
            ->asArray()
            ->all();
        foreach ( $halls as $hall){
            $ret[$hall['id']] = $hall['name'];
    }
        return $ret;

    }
}
