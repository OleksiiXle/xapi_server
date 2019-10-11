<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\kino\assets\KinoAsset;

KinoAsset::register($this);
$this->registerJs("
    var _cinema_hall = '';
    var _mode = 'create';
",\yii\web\View::POS_HEAD);

?>



<div class="container">
    <div class="row xHeader">
        <div class="col-md-12 col-lg-12">
            <?= Html::errorSummary($model)?>
        </div>
    </div>

    <div class="row xContent">

        <div class="col-md-12 col-lg-12">
            <div class="xCard ">
                <?php $form = ActiveForm::begin(['id' => 'form-create',]); ?>
                <?= $form->field($model, 'id')->hiddenInput()->label(false);?>
                <?= $form->field($model, 'cinema_hall')->textarea(['rows' => '5'  ])->hiddenInput()->label(false);?>
                <?= $form->field($model, 'name') ?>
                <div class="form-group" align="center">
                    <?= Html::button('Зберігти', [
                        'class' => 'btn btn-primary',
                        'name' => 'signup-button',
                        'onclick' => 'saveHall();'
                    ]) ?>
                    <?= Html::a('Відміна', '/kino/hall/index',[
                        'class' => 'btn btn-danger', 'name' => 'reset-button'
                    ]);?>
                </div>
                <?php ActiveForm::end(); ?>

            </div>
    </div>

    <!--*************************************************************************** КНОПКИ СОХРАНЕНИЯ -->
    <div class="row xContent">
        <div class="col-md-12 col-lg-12">
            <div class="row">
            </div>
        </div>
    </div>
    <!--*************************************************************************** МУЛЬТИПОЛЕ  -->
        <div class="xCard">
            <div class="row">
                <div id="rows" style="padding: 20px;"></div>
            </div>
            <div class="row">
                <div align="center">
                    <button class="btn btn-success" onclick="drawNewRow(ROW_LENGTH)">Добавить ряд</button>
                </div>
            </div>
        </div>
    </div>

</div>











