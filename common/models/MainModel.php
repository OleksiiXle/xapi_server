<?php

namespace common\models;

use Yii;

class MainModel extends \yii\db\ActiveRecord
{

    public function beforeSave($insert) {
        $this->updated_at = time();
      //  $this->updated_by = (isset(\Yii::$app->user->id)) ? \Yii::$app->user->id : 0;
        if ($insert){
            $this->created_at = time();
         //   $this->created_by = (isset(\Yii::$app->user->id)) ? \Yii::$app->user->id : 0;

        }
        return parent::beforeSave($insert);
    }

    public function getErrorsWithAttributesLabels()
    {
        $errorsArray = $this->getErrors();
        $ret = [];
        foreach ($errorsArray as $attributeName => $attributeErrors ){
            foreach ($attributeErrors as $attributeError)
            $ret[$this->getAttributeLabel($attributeName)] = $attributeError;
        }
        return $ret;
    }

    public function showErrors()
    {
        $ret = $lines = '';
        $header = '<p>' . Yii::t('yii', 'Please fix the following errors:') . '</p>';
        $errorsArray = $this->getErrorsWithAttributesLabels();
        foreach ($errorsArray as $attrName => $errorMessage){
            $lines .= "<li>$attrName : $errorMessage</li>";
        }
        if (!empty($lines)) {
            $ret = "<div>$header<ul>$lines</ul></div>" ;
        }

        return $ret;

    }

    public function validateNotEmpty($attribute, $params)
    {
        if (empty($this->$attribute)) {
            $this->addError($attribute, 'Необхідно заповнити ' . $this->attributeLabels()[$attribute]);
        }
    }



}
