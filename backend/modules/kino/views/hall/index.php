<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use common\components\widgets\xlegrid\Xlegrid;
use common\components\widgets\menuAction\MenuActionWidget;



$this->title =  'Кинозалы';
?>
<style>
    .usersGrid{
        padding: 5px;
    }
</style>
<div class="container">
    <div class="row ">
        <div class="xHeader">
            <div class="col-md-6" align="left">
            </div>
            <div class="col-md-6" align="right" >
                <?php
                echo Html::a( 'Новый зал', '/kino/hall/create', [
                    'class' =>'btn btn-primary',
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="row xContent">
        <div class="usersGrid xCard">
            <?php Pjax::begin([
                //   'id' => 'gridUsers',
                'id' => 'users-grid-container',
            ]);
            ?>
            <div id="users-grid" class="grid-view">
                <?php
                echo Xlegrid::widget([
                    'pager' => [
                        'firstPageLabel' => '<<<',
                        'lastPageLabel'  => '>>>'
                    ],
                    'dataProvider' => $dataProvider,
                    'gridTitle' => '',
                    'additionalTitle' => 'qq',
                    //-------------------------------------------
                    'tableOptions' => [
                        'class' => 'table table-bordered table-hover table-condensed',
                        'style' => ' width: 100%; table-layout: fixed;',
                    ],
                    //-------------------------------------------
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['style' => 'width: 3%;'],
                            'contentOptions' => ['style' => 'width: 3%;'],
                        ],
                        [
                            'attribute' => 'name',
                            'headerOptions' => ['style' => 'width: 87%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 87%; overflow: hidden'],
                        ],
                        [
                            'headerOptions' => ['style' => 'width: 10%; '],
                            'contentOptions' => [
                                'style' => 'width: 10%; ',
                            ],
                            'label'=>'',
                            'content'=>function($data){
                                return MenuActionWidget::widget(
                                    [
                                        'items' => [
                                                /*
                                            'Посмотреть' => [
                                                'icon' => 'glyphicon glyphicon-eye-open',
                                                'route' => Url::to(['/kino/hall/view', 'id' => $data['id']]),
                                            ],
                                                */
                                            'Изменить' => [
                                                'icon' => 'glyphicon glyphicon-pencil',
                                                'route' => Url::to(['/kino/hall/update', 'id' => $data['id']]),
                                            ],
                                            'Удалить' => [
                                                'icon' => 'glyphicon glyphicon-lock',
                                                'route' => Url::to(['/kino/hall/delete', 'id' => $data['id']]),
                                            ],
                                        ],
                                        'offset' => -100,

                                    ]
                                );
                            },
                        ],
                    ],

                ]);
                Pjax::end() ?>
            </div>
        </div>
    </div>

</div>





