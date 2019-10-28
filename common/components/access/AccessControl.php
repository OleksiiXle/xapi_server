<?php

namespace common\components\access;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\User;
use yii\di\Instance;
use yii\helpers\StringHelper;


/**
 * Access Control Filter (ACF) is a simple authorization method that is best used by applications that only need some simple access control.
 * As its name indicates, ACF is an action filter that can be attached to a controller or a module as a behavior.
 * ACF will check a set of access rules to make sure the current user can access the requested action.
 *
 * To use AccessControl, declare it in the application config as behavior.
 * For example.
 *
 * ```
 * 'as access' => [
 *     'class' => 'mdm\admin\components\AccessControl',
 *     'allowActions' => ['site/login', 'site/error']
 * ]
 * ```
 *
 * @property User $user
 *
 */
class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var User User for check access.
     */
    private $_user = 'user';

    /**
     * @var array List of action that not need to check access.
     */
    public $allowActions = [];

    public $rules = [];

    /**
     * @var array the default configuration of access rules. Individual rule configurations
     * specified via [[rules]] will take precedence when the same property of the rule is configured.
     */
    //   public $ruleConfig = ['class' => 'yii\filters\AccessRule'];
    public $ruleConfig = ['class' => AccessRule::class];

    public function init()
    {
        $rrr = 1;
        parent::init();
        if ($this->user !== false) {
            $this->user = Instance::ensure($this->user, User::className());
        }
        foreach ($this->rules as $i => $rule) {
            if (is_array($rule)) {
                $this->rules[$i] = Yii::createObject(array_merge($this->ruleConfig, $rule));
            }
        }
    }

    /** Get user
     * @return User
     */
    public function getUser()
    {
        $r=1;
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }


    public function beforeAction($action)
    {
        if (isset($action->controller->module) && is_a($action->controller->module, 'yii\debug\Module')){
            return true;
        }


        $user = $this->user;
     //   \yii::trace('************************************************ access user=' . $user->id, "dbg");
     //   \yii::trace(\yii\helpers\VarDumper::dumpAsString($user), "dbg");

        $request = Yii::$app->getRequest();
        /* @var $rule AccessRule */
        if (!empty($this->rules)){
            foreach ($this->rules as $rule) {
                if ($allow = $rule->allows($action, $user, $request)) {
                    return true;
                } elseif ($allow === false) {
                    if (isset($rule->denyCallback)) {
                        call_user_func($rule->denyCallback, $rule, $action);
                    } elseif ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $rule, $action);
                    } else {
                        $this->denyAccess($user);
                    }
                    return false;
                }
            }
        }

        $this->denyAccess($user);
    }


    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param  User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        $request = Yii::$app->getRequest();
        $url = $request->getUrl();
        $errMessage = "Дія заборонена *** $url ***";
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
         //   throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            \yii::$app->getSession()->addFlash("warning",$errMessage);

            if (\Yii::$app->request->isAjax){
                throw new ForbiddenHttpException($errMessage);
            } else {
               // \yii::$app->getSession()->addFlash("warning",$errMessage);
                $r = Yii::$app->request->referrer;
                if (!empty($r)){
                    Yii::$app->response->redirect($r);
                } else {
                    throw new ForbiddenHttpException($errMessage);
                }
               // Yii::$app->response->redirect(Url::to('site/notactivated'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        if (1 == 1 ){
            //todo когда "as access" уйдет из web.php эту хреновину убрать
            if (empty($this->rules)){
                $controllerBehaviors = $action->controller->behaviors();
                foreach ($controllerBehaviors as $name => $data){
                    if ($name == 'access' && $data['class'] == $this::className() && isset($data['rules'])){
                        $this->rules = $data['rules'];
                        foreach ($this->rules as $i => $rule) {
                            if (is_array($rule)) {
                                $this->rules[$i] = Yii::createObject(array_merge($this->ruleConfig, $rule));
                            }
                        }

                    }
                }
            }
         //   $q1 = $action->controller->hasMethod('allowAction');
            $id = $this->getActionId($action);

            if (empty($this->only)) {
                $onlyMatch = true;
            } else {
                $onlyMatch = false;
                foreach ($this->only as $pattern) {
                    if (StringHelper::matchWildcard($pattern, $id)) {
                        $onlyMatch = true;
                        break;
                    }
                }
            }

            $exceptMatch = false;
            foreach ($this->except as $pattern) {
                if (StringHelper::matchWildcard($pattern, $id)) {
                    $exceptMatch = true;
                    break;
                }
            }

            return !$exceptMatch && $onlyMatch;
        }
    }
}
