<?php

namespace frontend\modules\v1\models;

use common\models\Functions;
use common\models\MainModel;
use Yii;

class KinoSeans extends MainModel
{
    public $dataText = '';

    private $_hallName;

    private $_hallMatrix;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kino_seans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hall_id' ,'data', 'created_at', 'updated_at', 'reservations_count'], 'integer'],
            [['hall_id', 'cinema_hall'], 'required'],
            [['cinema_hall', 'dataText'], 'string'],
            [['filmName'], 'string', 'min' => 3, 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filmName' => 'Название фильма',
            'hall_id' => 'Кинозал',
            'cinema_hall' => 'Кино-зал',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'hallName' => 'Кинозал',
            'dataText' => 'Дата, время',
            'reservations_count' => 'Количество заказов билетов',
        ];
    }

    public function fields() {
        $fields = ['id', 'hallName', 'dataText', 'filmName', 'hall_id', 'cinema_hall'];

        return $fields;
    }

    public function getHall()
    {
        return $this->hasOne(Kino::className(), ['id' => 'hall_id']);
    }

    /**
     * @return mixed
     */
    public function getHallMatrix()
    {
        $this->_hallMatrix = [];
        if (!empty($this->cinema_hall)){
            $this->_hallMatrix = json_decode($this->cinema_hall, true);
        }
        return $this->_hallMatrix;
    }



    public function reservation($reservation)
    {
        /*
             array (size=3)
      0 =>
        array (size=3)
          'rowNumber' => string '1' (length=1)
          'seatNumber' => string '4' (length=1)
          'persona' => string 'lokoko' (length=6)
      1 =>
        array (size=3)
          'rowNumber' => string '2' (length=1)
          'seatNumber' => string '3' (length=1)
          'persona' => string 'lokoko' (length=6)
      2 =>
        array (size=3)
          'rowNumber' => string '4' (length=1)
          'seatNumber' => string '5' (length=1)
          'persona' => string 'lokoko' (length=6)

         */
        $result = [
            'status' => false,
            'data' => 'error',
        ];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $hall = $this->hallMatrix;
            //-- check
            foreach ($reservation as $reservate){
                if ( $hall[$reservate['rowNumber']][$reservate['seatNumber']]['status'] !== 'free'){
                    $transaction->rollBack();
                    $result['data'] = 'Место ' . $reservate['rowNumber'] . '-' . $reservate['seatNumber'] .' уже забронировано';
                    return $result;
                }
            }
            //-- reservation
            $reservationId = $this->id . '-' . $this->reservations_count++;
            foreach ($reservation as $reservate){
                $hall[$reservate['rowNumber']][$reservate['seatNumber']]['status'] = 'taken';
                $hall[$reservate['rowNumber']][$reservate['seatNumber']]['persona'] = $reservationId;
            }
            $this->cinema_hall = json_encode($hall);
            if (!$this->save()){
                $transaction->rollBack();
                $result['data'] = $this->getErrorsWithAttributesLabels();
                return $result;
            }
            $transaction->commit();
            $result = [
                'status' => true,
                'data' => [
                    'seansId' => $this->id,
                    'seansData' => $this->dataText,
                    'seansHall' => $this->hallName,
                    'seansFilmName' => $this->filmName,
                    'seansResrvationId' => $reservationId,
                    'seats' => $reservation,
                ],
            ];
            return $result;
        } catch (\Exception $e){
            if ($transaction->isActive) {
                $transaction->rollBack();
            }
            $result['data'] = $e->getMessage();
            return $result;
        }
    }



    /**
     * @return mixed
     */
    public function getHallName()
    {
        $this->_hallName = '';
        if (!empty($this->hall)){
            $this->_hallName = $this->hall->name;
        }
        return $this->_hallName;
    }



    public function saveSeans()
    {
        $i = 1;
        $hall = Kino::findOne($this->hall_id);
        $this->cinema_hall = $hall->cinema_hall;
        return $this->save();
    }

    public function afterFind()
    {
        $this->dataText = (isset($this->data)) ? Functions::intToDateTime($this->data) : '';
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeValidate()
    {
        if (isset($this->dataText) && is_string($this->dataText) && $this->dataText !=''){
            $this->data = Functions::dateTimeToInt($this->dataText);
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

}
