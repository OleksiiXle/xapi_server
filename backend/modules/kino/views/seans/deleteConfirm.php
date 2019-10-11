<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use backend\modules\kino\models\Kino;

?>


<div class="container">
    <div class="row xContent">
        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="xCard ">
                    <?php $form = ActiveForm::begin(['id' => 'form-create',]); ?>
                    <?= $form->field($model, 'id')->hiddenInput()->label(false);?>
                    <?= $form->field($model, 'filmName') ?>
                    <?= $form->field($model, 'hall_id' )
                        ->dropDownList(Kino::hallsList(),
                            ['options' => [ $model->hall_id => ['Selected' => true]],]);?>
                    <?= $form->field($model, 'dataText')->widget(DateTimePicker::className(),[
                        'name' => 'dp_1',
                        'type' => DateTimePicker::TYPE_INPUT,
                        'options' => ['placeholder' => 'Ввод даты/времени...'],
                        'convertFormat' => true,
                        'value'=> date("d.m.Y h:i",(integer) $model->dataText),
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose'=>true,
                            'weekStart'=>1, //неделя начинается с понедельника
                            //   'startDate' => '01.05.2015 00:00', //самая ранняя возможная дата
                            'todayBtn'=>true, //снизу кнопка "сегодня"
                        ]
                    ])->label(false);  ?>

                    <div class="form-group" align="center">
                        <?= Html::submitButton('Удалить', [
                            'class' => 'btn btn-primary',
                            'name' => 'signup-button',
                        ]) ?>
                        <?= Html::a('Отмена', '/kino/seans/index',[
                            'class' => 'btn btn-danger', 'name' => 'reset-button'
                        ]);?>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>

</div>









