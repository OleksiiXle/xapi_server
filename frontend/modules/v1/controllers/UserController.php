<?php

namespace frontend\modules\v1\controllers;

use common\models\UserM;
use frontend\modules\oauth2\TokenAuth;
use frontend\modules\v1\models\KinoSeans;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\rest\Controller;
use yii\web\Response;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            // performs authorization by token
            'tokenAuth' => [
                'class' => TokenAuth::className(),
            ],
        ];
    }
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
    protected function verbs()
    {
        return [
            'userinfo' => ['POST'],
        ];
    }

    public function actionUserinfo()
    {

        if ($userId = \Yii::$app->request->post('id')){
            $user = UserM::findOne($userId);
            $userProfile = $user->userProfileForApi;
          //  \yii::trace('************************************************ $userProfileo ', "dbg");
         //   \yii::trace(\yii\helpers\VarDumper::dumpAsString($userProfile), "dbg");

            return $userProfile;
        } else {
            throw new NotFoundHttpException("User $userId not found");
        }
    }


}
