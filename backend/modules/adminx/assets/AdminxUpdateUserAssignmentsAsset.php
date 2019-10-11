<?php

namespace backend\modules\adminx\assets;

use yii\web\AssetBundle;

class AdminxUpdateUserAssignmentsAsset extends  AssetBundle {
    //public $baseUrl = '@web/modules/adminx/assets';
    public $sourcePath = '@app/modules/adminx/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
    ];
    public $js = [
        'js/updateUserAssignments.js',
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}