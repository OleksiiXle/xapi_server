<?php

namespace frontend\modules\oauth2\granttypes;

use conquer\oauth2\BaseModel;
use conquer\oauth2\Exception;
use frontend\modules\oauth2\models\AccessToken;
use conquer\oauth2\models\AuthorizationCode;
use conquer\oauth2\models\RefreshToken;
use Yii;
use yii\web\NotFoundHttpException;

class Logout extends BaseModel
{
    /**
     * @var AuthorizationCode
     */
    private $_authCode;

    /**
     * Value MUST be set to "authorization_code".
     * @var string
     */
    public $grant_type;

    /**
     * The authorization code received from the authorization server.
     * @var string
     */
    public $code;

    /**
     * REQUIRED, if the "redirect_uri" parameter was included in the
     * authorization request as described in Section 4.1.1, and their
     * values MUST be identical.
     * @link https://tools.ietf.org/html/rfc6749#section-4.1.1
     * @var string
     */
    public $redirect_uri;

    /**
     *
     * @var string
     */
    public $client_id;

    /**
     * Access Token Scope
     * @link https://tools.ietf.org/html/rfc6749#section-3.3
     * @var string
     */
    public $scope;

    /**
     * @var string
     */
    public $client_secret;

    public $user_id;


    public function rules()
    {
        return [
            [['user_id', 'client_id', 'grant_type', 'client_secret'], 'required'],
            [['user_id'], 'integer'],
            [['client_id'], 'string', 'max' => 80],
            [['redirect_uri'], 'url'],
            [['client_id'], 'validateClientId'],
            [['client_secret'], 'validateClientSecret'],
        ];
    }


    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     * @throws \Throwable
     * @throws \conquer\oauth2\RedirectException
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function getResponseData()
    {
        $ret1 = AccessToken::deleteAll(['client_id' => $this->client_id, 'user_id' => $this->user_id]);
        $ret2 = RefreshToken::deleteAll(['client_id' => $this->client_id, 'user_id' => $this->user_id]);
        $ret = [
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'deleted_tokens' => $ret1,
            'deleted_refresh_tokens' => $ret2,
        ];
        \yii::trace('************************************************ LOGOUT', "dbg");
        \yii::trace(\yii\helpers\VarDumper::dumpAsString($this->getAttributes()), "dbg");
        \yii::trace(\yii\helpers\VarDumper::dumpAsString($ret), "dbg");


        return $ret;
    }


}
