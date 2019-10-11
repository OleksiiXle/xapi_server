<?php
namespace backend\modules\kino\controllers;

use backend\modules\adminx\controllers\MainController;
use backend\modules\kino\models\Kino;
use backend\modules\kino\models\KinoSeans;
use Yii;
use backend\modules\adminx\components\AccessControl;
use yii\web\Controller;
use common\components\conservation\ActiveDataProviderConserve;


class SeansController extends MainController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'      => true,
                    'actions'    => [
                        'index', 'create', 'update', 'delete', 'view',
                    ],
                    'roles'      => ['systemAdminxx', ],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $model = KinoSeans::find();
        $dataProvider = new ActiveDataProviderConserve([
            // 'searchId' => $id,
            'baseModel' => $model,
            'conserveName' => 'seansAdminGrid',
            'pageSize' => 15,
            'sort' => ['attributes' => [
                'id',
                'name',
                'data' => [
                    'asc' => [
                        'data' => SORT_ASC,
                    ],
                    'desc' => [
                        'data' => SORT_DESC,
                    ],
                ],
            ]],

        ]);
        return $this->render('index',[
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new KinoSeans();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveSeans()) {
                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {

        $model = KinoSeans::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveSeans()) {
                return $this->redirect('index');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {

        $model = KinoSeans::findOne($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = KinoSeans::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->delete()) {
                return $this->redirect('index');
            }
        }
        return $this->render('deleteConfirm', [
            'model' => $model,
        ]);
    }


}
