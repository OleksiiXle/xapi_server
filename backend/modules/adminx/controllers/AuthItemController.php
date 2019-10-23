<?php
namespace backend\modules\adminx\controllers;

use common\components\conservation\ActiveDataProviderConserve;
use common\components\access\AccessControl;
use backend\modules\adminx\models\AuthItem;
use backend\modules\adminx\models\filters\AuthItemFilter;
use yii\rbac\Item;

/**
 * Class AuthItemController
 * Управление разрешениями и ролями
 * @package app\modules\adminx\controllers
 */
class AuthItemController extends MainController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'      => true,
                    'actions'    => [
                        'index',
                    ],
                    'roles'      => ['systemAdminxx' ],
                ],
                [
                    'allow'      => true,
                    'actions'    => [
                        'create', 'update',  'delete', 'assign', 'revoke'
                    ],
                    'roles'      => ['systemAdminxx', ],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $q=1;
        $dataProvider = new ActiveDataProviderConserve([
            'filterModelClass' => AuthItemFilter::class,
            'conserveName' => 'authItemAdminGrid',
            'pageSize' => 15,
        ]);
        return $this->render('index',[
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate($type)
    {
        $model = new AuthItem();
        $model->type = $type;
        if ($model->load(\Yii::$app->getRequest()->post())) {
            if ($model->save()) {
                return $this->redirect(['/adminx/auth-item/update', 'name' => $model->name]);
            }
        }
        return $this->render('create',
            [
                'model' => $model,
            ]);
    }

    public function actionUpdate($name )
    {
        $model =  AuthItem::find()
            ->where(['name' => $name])
            ->one();
        if (isset($model)){
            $assigments = AuthItem::getItemsXle($model->type, $name);
            if ($model->load(\Yii::$app->getRequest()->post())) {
                if (\Yii::$app->getRequest()->post('delete-button')){
                    $manager = \Yii::$app->authManager;
                    $item = ($model->type == AuthItem::TYPE_ROLE) ?
                        $manager->getRole($model->name) :
                        $manager->getPermission($model->name);
                    $manager->remove($item);
                    return $this->redirect('/adminx/auth-item');
                }
                if ($model->save()) {
                    return $this->redirect('/adminx/auth-item');
                }
            }
            return $this->render('update', [
                'model' => $model,
                'assigments' => $assigments,
                ]);

        } else {
            return $this->redirect('/adminx/auth-item');
        }
    }

    public function actionDelete()
    {

    }

    /**
     * +++ Назначение итему ролей, разрешений, роутов
     * @param string $id
     * @param string $type (roles, permissions, routs)
     * @param array $items
     * @return string
     */
    public function actionAssign()
    {
        try {
            $name    = \Yii::$app->getRequest()->post('name');
            $type    = \Yii::$app->getRequest()->post('type');
            $items = \Yii::$app->getRequest()->post('items', []);
            $auth = \Yii::$app->getAuthManager();
            $parent = $type == Item::TYPE_ROLE ? $auth->getRole($name) : $auth->getPermission($name);
            foreach ($items as $itemName){
                if (($item = $auth->getPermission($itemName)) == null){
                    $item = $auth->getRole($itemName);
                }
                $success = $auth->addChild($parent, $item);
            }
            $assigments = AuthItem::getItemsXle($type, $name);

            $this->result =[
                'status' => true,
                'data'=>  $assigments
            ];
        } catch (\Exception $e) {
            $this->result['data'] = $e->getMessage();
        }
        return $this->asJson($this->result);
    }

    /**
     * +++ Удаление у итема ролей, разрешений, роутов
     * @param string $id
     * @param string $type (roles, permissions, routs)
     * @param array $items
     * @return string
     */
    public function actionRevoke()
    {
        try {
            $name    = \Yii::$app->getRequest()->post('name');
            $type    = \Yii::$app->getRequest()->post('type');
            $items = \Yii::$app->getRequest()->post('items', []);
            $auth = \Yii::$app->getAuthManager();
            $parent = $type == Item::TYPE_ROLE ? $auth->getRole($name) : $auth->getPermission($name);
            foreach ($items as $itemName){
                if (($item = $auth->getPermission($itemName)) == null){
                    $item = $auth->getRole($itemName);
                }
                $success = $auth->removeChild($parent, $item);
            }
            $assigments = AuthItem::getItemsXle($type, $name);

            $this->result =[
                'status' => true,
                'data'=>  $assigments
            ];
        } catch (\Exception $e) {
            $this->result['data'] = $e->getMessage();
        }
        return $this->asJson($this->result);
    }




}
