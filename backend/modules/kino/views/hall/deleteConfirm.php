<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\kino\assets\KinoAsset;

?>


<div class="container">
    <div class="row xContent">
        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="xCard">
                    <?php $form = ActiveForm::begin(['id' => 'form-delete',]); ?>
                    <?= $form->field($model, 'id')->hiddenInput()->label(false);?>
                    <?= $form->field($model, 'name')->textInput(['disabled']) ?>
                    <div class="form-group" align="center">
                        <?= Html::submitButton('Удалить', [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Отмена', '/kino/hall/index',[
                            'class' => 'btn btn-danger', 'name' => 'reset-button'
                        ]);?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>









