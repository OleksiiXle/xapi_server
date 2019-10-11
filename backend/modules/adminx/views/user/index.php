<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use common\components\widgets\xlegrid\Xlegrid;
use common\components\widgets\menuAction\MenuActionWidget;
use backend\modules\adminx\models\UserM;
use backend\modules\adminx\assets\AdminxUserAsset;

AdminxUserAsset::register($this);


$this->title =  'Користувачі';
?>
<style>
    .usersGrid{
        padding: 5px;
      /*  padding: 0;
        margin: -13px; */
   /*    background-color: #dbdbdb*/
    }
</style>

<div class="row ">
    <div class="xHeader">
        <div class="col-md-6" align="left">
        </div>
        <div class="col-md-6" align="right" >
            <?php
            echo Html::a( 'Рєєстрація нового користувача', '/adminx/user/signup-by-admin', [
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
                    'filterView' => '@app/modules/adminx/views/user/_filterUser',
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
                            'attribute' => 'id',
                            'headerOptions' => ['style' => 'width: 3%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 3%; overflow: hidden'],
                        ],
                        [
                            'attribute' => 'username',
                            'headerOptions' => ['style' => 'width: 10%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 10%; overflow: hidden'],
                        ],
                        [
                            'attribute' => 'nameFam',
                            'headerOptions' => ['style' => 'width: 10%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 10%; overflow: hidden'],
                        ],
                        [
                            'attribute' => 'nameNam',
                            'headerOptions' => ['style' => 'width: 7%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 7%; overflow: hidden'],
                        ],
                        [
                            'attribute' => 'nameFat',
                            'headerOptions' => ['style' => 'width: 7%; overflow: hidden;'],
                            'contentOptions' => ['style' => 'width: 7%; overflow: hidden'],
                        ],
                        [
                            'attribute' => 'userRoles',
                            'headerOptions' => ['style' => 'width: 8%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 8%; overflow: hidden;'],
                        ],
                        [
                            'attribute' => 'lastVisitTimeTxt',
                            'label' => 'Час ост. дії',
                            'headerOptions' => ['style' => 'width: 8%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 8%; white-space: nowrap; overflow: hidden;'],
                        ],
                        [
                            'attribute' => 'status',
                            'headerOptions' => ['style' => 'width: 6%;overflow: hidden; '],
                            'contentOptions' => ['style' => 'width: 6%; white-space: nowrap; overflow: hidden;'],
                            'label'=>'Активність',
                            'content'=>function($data){
                                return Html::a('<span class="glyphicon glyphicon-star"></span>', false,
                                    [
                                        'style' => ($data->status == UserM::STATUS_ACTIVE)
                                            ? 'color: red;' : 'color: grey;',
                                        'title' => ($data->status == UserM::STATUS_ACTIVE)
                                            ? 'Активувати' : 'Деактивувати',
                                        'onclick' => 'changeUserActivity("' . $data->id . '");',
                                        'id' => 'activityIcon_' . $data->id,
                                    ]);
                            },
                        ],
                        [
                            'headerOptions' => ['style' => 'width: 3%; '],
                            'contentOptions' => [
                                'style' => 'width: 3%; ',
                            ],
                            'label'=>'',
                            'content'=>function($data){
                                return MenuActionWidget::widget(
                                    [
                                        'items' => [
                                            'Перегляд інформації' => [
                                                'icon' => 'glyphicon glyphicon-eye-open',
                                                'route' => Url::to(['/adminx/user/view', 'id' => $data['id']]),
                                            ],
                                            'Змінити данні' => [
                                                'icon' => 'glyphicon glyphicon-pencil',
                                                'route' => Url::to(['/adminx/user/update-by-admin', 'id' => $data['id']]),
                                            ],
                                            'Змінити дозвіли та ролі' => [
                                                'icon' => 'glyphicon glyphicon-lock',
                                                'route' => Url::to(['/adminx/user/update-user-assignments', 'id' => $data['id']]),
                                            ],
                                            'Переглянути консерву' => [
                                                'icon' => 'glyphicon glyphicon-lock',
                                                'route' => Url::to(['/adminx/user/conservation', 'id' => $data['id']]),
                                            ],
                                        ],
                                        'offset' => -200,

                                    ]
                                );
                            },
                        ],

                        /*
                                          [
                                              'attribute'=>'id',
                                              'headerOptions' => ['style' => 'width:5%'],
                                              'label'=>'personal_id',
                                              'format'=>'text', // Возможные варианты: raw, html
                                              'content'=>function($data){
                                                  return (isset($data->userDatas->personal_id)) ? $data->userDatas->personal_id : '';
                                              },
                                          ],
                                          [
                                              'attribute' => 'lastRoutTime',
                                              'headerOptions' => ['style' => 'width:10%'],
                                          ],
                                          //    'lastRout',
                                          [
                                              'attribute'=>'phone',
                                              'headerOptions' => ['style' => 'width:10%'],
                                              'label'=>'Телефон',
                                              'format'=>'text', // Возможные варианты: raw, html
                                              'content'=>function($data){
                                                  return (isset($data->userDatas->phone)) ? $data->userDatas->phone : '';
                                              },
                                          ],

                                          [
                                              'label'=> \Yii::t('app', 'Статус'),
                                              'headerOptions' => ['style' => 'width:5%'],
                                              'content'=>function($data){
                                                  return \Yii::t('app', \app\modules\adminx\models\UserM::STATUS_DICT[$data->status]);
                                              },
                                          ],
                                          */
                        //------------------------------
                    ],

                ]);
                Pjax::end() ?>

            </div>

        </div>
</div>





