<?php

namespace console\controllers;

use backend\modules\adminx\models\UserData;
use backend\modules\adminx\models\UserM;
use common\models\Menu;

class InitController extends \yii\console\Controller
{
    public function actionHello()
    {
        echo 'hello' . PHP_EOL;
    }

    public function actionMenuInit() {
        echo 'МЕНЮ *******************************' .PHP_EOL;
        $delCnt = Menu::deleteAll();
        echo 'Удалено ' . $delCnt . ' пунктов меню ' .PHP_EOL;

        $menus = require(__DIR__ . '/data/menuInit.php');
        $sort1 = $sort2 = $sort3 = 1;
        foreach ($menus as $menu1){
            echo $menu1['name'] . PHP_EOL;
            $m1 = new Menu();
            $m1->parent_id = 0;
            $m1->sort = $sort1++;
            $m1->name = $menu1['name'];
            $m1->route = $menu1['route'];
            $m1->role = $menu1['role'];
            $m1->access_level = $menu1['access_level'];
            if (!$m1->save()){
                echo var_dump($m1->getErrors()) . PHP_EOL;
                return true;
            }
            foreach ($menu1['children'] as $menu2){
                echo ' --- ' . $menu2['name'] . PHP_EOL;
                $m2 = new Menu();
                $m2->parent_id = $m1->id;
                $m2->sort = $sort2++;
                $m2->name = $menu2['name'];
                $m2->route = $menu2['route'];
                $m2->role = $menu2['role'];
                $m2->access_level = $menu2['access_level'];
                if (!$m2->save()){
                    echo var_dump($m2->getErrors()) . PHP_EOL;
                    return true;
                }
                foreach ($menu2['children'] as $menu3){
                    echo ' --- --- ' . $menu3['name'] . PHP_EOL;
                    $m3 = new Menu();
                    $m3->parent_id = $m2->id;
                    $m3->sort = $sort3++;
                    $m3->name = $menu3['name'];
                    $m3->route = $menu3['route'];
                    $m3->role = $menu3['role'];
                    $m3->access_level = $menu3['access_level'];
                    if (!$m3->save()){
                        echo var_dump($m3->getErrors()) . PHP_EOL;
                        return true;
                    }
                }
                $sort3 = 1;
            }
            $sort2 = 1;
        }
        return true;
    }

    public function actionAuthInit(){
        echo '************************************************************************** ОБЩЕЕ МЕНЮ' . PHP_EOL;
        $params = require(__DIR__ . '/data/authinit.php');
        $permissions      = $params['permissions'];
        $roles            = $params['roles'];
        $rolesPermissions = $params['rolesPermissions'];
        $rolesChildren    = $params['rolesChildren'];
        $auth = \Yii::$app->authManager;
        $rolesOld = $auth->getRoles();
        //-- добавляем роли, которых не было
        foreach ($roles as $roleName => $roleNote){
            echo '* роль * ' . $roleName ;
            $checkRole = $auth->getRole($roleName);
            if (!isset($checkRole)){
                echo ' добавляю' .PHP_EOL;
                $newRole = $auth->createRole($roleName);
                $newRole->description = $roleNote;
                $auth->add($newRole);
            } else {
                echo ' уже есть' . PHP_EOL;
            }
        }
        //-- добавляем разрешения, которых не было
        foreach ($permissions as $permission => $description){
            echo '* дозвіл * ' . $permission ;
            $checkRole = $auth->getPermission($permission);
            if (!isset($checkRole)){
                echo ' добавляю' .PHP_EOL;
                $newPermission = $auth->createPermission($permission);
                $newPermission->description = $description;
                $auth->add($newPermission);
            } else {
                echo ' уже есть' . PHP_EOL;
            }
        }
        //-- добавляем ролям детей, которых не было
        foreach ($rolesChildren as $role => $children){
            echo '* діти ролі * ' . $role . PHP_EOL;
            $parentRole = $auth->getRole($role);
            foreach ($children as $child){
                echo ' добавляю' . ' ' . $child . PHP_EOL;
                try{
                    $childRole = $auth->getRole($child);
                    $auth->addChild($parentRole, $childRole);
                } catch (\yii\base\Exception $e){
                    echo ' мабуть вже є така дитинка' . ' ' . $child . PHP_EOL;
                }
            }

        }
        //-- добавляем ролям разрешения, которых не было
        foreach ($rolesPermissions as $role => $permission){
            echo '* дозвіли ролі * ' . $role . PHP_EOL;
            $parentRole = $auth->getRole($role);
            foreach ($permission as $perm){
                echo ' добавляю' . ' ' . $perm ;
                try{
                    $rolePermission = $auth->getPermission($perm);
                    if (isset($rolePermission)){
                        $auth->addChild($parentRole, $rolePermission);
                        echo ' OK' . PHP_EOL;
                    } else {
                        echo ' упс... такого дозвілу ще немає' . PHP_EOL;
                        exit();
                    }
                } catch (\yii\base\Exception $e){
                    echo ' мабуть вже є така дозвіл' . ' ' . $perm . PHP_EOL;
                }
            }

        }
        return true;
    }

    public function actionUserInit(){
        echo 'ДЕФОЛТНЫЕ ПОЛЬЗОВАТЕЛИ *******************************' .PHP_EOL;
        $delCnt = UserM::deleteAll();
        echo 'Удалено ' . $delCnt . ' пользователей ' .PHP_EOL;

        $users = require(__DIR__ . '/data/usersinit.php');
        $auth = \Yii::$app->authManager;
        foreach ($users as $user){
            echo $user['username'] . PHP_EOL;
            //   echo var_dump($user);
            $model = new UserM();
            //   $model->scenario = User::SCENARIO_REGISTRATION;
            $model->setAttributes($user);
            $model->setPassword($user['password']);
            $model->generateAuthKey();
            if (!$model->save()){
                echo var_dump($model->getErrors()) . PHP_EOL;
                return false;
            }
            $userData = new UserData();
            $userData->setAttributes($user);
            $userData->user_id = $model->id;
            if (!$userData->save()){
                echo var_dump($userData->getErrors()) . PHP_EOL;
                return false;
            }
            foreach ($user['userRoles'] as $role){
                $userRole = $auth->getRole($role);
                if (isset($userRole)){
                    $auth->assign($userRole, $model->id);
                    echo '   ' . $role . PHP_EOL;
                } else {
                    echo '   не найдена роль - ' . $role . PHP_EOL;
                }

            }
        }
        return true;
    }

    public function actionRemoveAll()
    {
        //--- очистить таблицы MenuX, User, удалить все рполи и разрешения
        echo '************************************************************************** ОЧИСТКА ДАННЫХ' . PHP_EOL;
        $delCnt = UserM::deleteAll();
        echo 'Удалено ' . $delCnt . ' пользователей ' .PHP_EOL;
        $delCnt = Menu::deleteAll();
        echo 'Удалено ' . $delCnt . ' пунктов меню ' .PHP_EOL;
        $auth = \Yii::$app->authManager;
        $auth->removeAll();
        echo 'Удалены все роли и разрешения' .PHP_EOL;
        $a = \Yii::$app->db->createCommand('ALTER TABLE user AUTO_INCREMENT=1')->execute();
        $a = \Yii::$app->db->createCommand('ALTER TABLE auth_rule AUTO_INCREMENT=1')->execute();
        $a = \Yii::$app->db->createCommand('ALTER TABLE auth_item AUTO_INCREMENT=1')->execute();
        $a = \Yii::$app->db->createCommand('ALTER TABLE auth_item_child AUTO_INCREMENT=1')->execute();
        $a = \Yii::$app->db->createCommand('ALTER TABLE auth_assignment AUTO_INCREMENT=1')->execute();
        $a = \Yii::$app->db->createCommand('ALTER TABLE menu AUTO_INCREMENT=1')->execute();

    }

    public function actionInit()
    {
        $this->actionRemoveAll();
        $this->actionMenuInit();
        $this->actionAuthInit();
        $this->actionUserInit();
    }

}