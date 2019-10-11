<?php

namespace common\components\widgets\menuG;

use common\components\widgets\menuG\models\MenuG;
use yii\widgets\InputWidget;

class MenuGWidget extends InputWidget
{


    public function init()
    {
       // parent::init();
    }

    public function run()
    {
        $i=1;
        if (\Yii::$app->user->isGuest){
            $menus = [''];
        } else {
            $user_id = \Yii::$app->user->getId();
          //  $userAssignments = \Yii::$app->getAuthManager()->getAssignments($user_id);
            $userAssignments = \Yii::$app->getAuthManager()->getPermissionsByUser($user_id);
            $menus = ['menuAll'];
            foreach ($userAssignments as $name => $data){
                if (substr($name, 0,4) === 'menu'){
                    $menus[] = $name;
                }
            }
        }
        MenuGAssets::register($this->getView());
        $tree = MenuG::find()
            ->orderBy('parent_id, sort')
            ->where(['in', 'role', $menus])
            ->asArray()
            ->all();
        $html = MenuG::getTree($tree,0);
        return $this->render('menuG',
            [
                'html' => $html,
                'tree' => $tree,
            ]);
    }

}
