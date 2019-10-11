<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\modules\adminx\models\AuthItem;
use common\components\widgets\xlegrid\Xlegrid;

?>

<?php

//\app\modules\adminx\assets\AdminxxUpdateAuthItemAsset::register($this);

$this->title =  'Дозвіли, ролі';

?>
<div class="row ">
    <div class="xHeader">
        <div class="col-md-6" align="left">
        </div>
        <div class="col-md-6" align="right" >
            <?php
            echo Html::a('Створити роль', ['/adminx/auth-item/create', 'type' => AuthItem::TYPE_ROLE],
                [
                    'class' =>'btn btn-primary',
                ]);
            echo '  ';
            echo Html::a('Створити дозвіл', ['/adminx/auth-item/create', 'type' => AuthItem::TYPE_PERMISSION], [
                'class' =>'btn btn-primary',
            ]);
            echo '  ';
            ?>
        </div>
    </div>

</div>
<div class="row xContent">
    <div class="xCard">
        <?php Pjax::begin(['id' => 'gridPermission']);
        echo Xlegrid::widget([
            'dataProvider' => $dataProvider,
            'gridTitle' => '',
            'additionalTitle' => 'qq',
            'filterView' => '@app/modules/adminx/views/auth-item/_authItemFilter',
            //-------------------------------------------
            'tableOptions' => [
                'class' => 'table table-bordered table-hover table-condensed',
            //  'style' => ' width: 100%; table-layout: fixed;',
            ],

            //-------------------------------------------
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label'=>'Тип',
                    'content'=>function($data){
                        $ret = '';
                        switch ($data->type){
                            case AuthItem::TYPE_ROLE:
                                $ret = 'Роль';
                                break;
                            case AuthItem::TYPE_PERMISSION:
                                $ret =  'Дозвіл';
                                break;
                        }
                        return $ret;
                    },
                ],

                'name',
                'description',
                'rule_name',
                ['class' => 'yii\grid\ActionColumn',
                    'buttons'=>[
                        'update'=>function($url, $data) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                Url::toRoute(['/adminx/auth-item/update',
                                    'name' => $data['name'],

                                ]  ),
                                [
                                    'title' => \Yii::t('app', 'Редагувати'),
                                ]);

                        },
                    ],
                    'template'=>' {update}',

                ],
            ],
            //------------------------------

        ]);
        Pjax::end() ?>

    </div>

</div>

