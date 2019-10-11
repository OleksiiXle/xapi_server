<?php
use yii\helpers\Html;
use backend\modules\kino\assets\SeansViewAsset;

backend\modules\kino\assets\SeansViewAsset::register($this);

$this->registerJs("
    var _cinema_hall = '{$model->cinema_hall}';
",\yii\web\View::POS_HEAD);

?>



<div class="container">
    <div class="row xContent">

        <div class="col-md-12 col-lg-12">
            <div class="xCard ">
                <div class="form-control">
                    <?=Html::encode($model->filmName);?>
                </div>
                <div class="form-control">
                    <?=Html::encode($model->hallName);?>
                </div>
                <div class="form-control">
                    <?=Html::encode($model->dataText);?>
                </div>
            </div>
            <div class="xCard">
                <div id="rows" style="padding: 20px;"></div>
            </div>
            <div class="form-group" align="center">
                <?= Html::a('Вернуться', '/kino/seans/index',[
                    'class' => 'btn btn-danger', 'name' => 'reset-button'
                ]);?>
            </div>

        </div>

</div>











