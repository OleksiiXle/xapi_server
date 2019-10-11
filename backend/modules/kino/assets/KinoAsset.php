<?php

namespace backend\modules\kino\assets;

use yii\web\AssetBundle;

class KinoAsset extends  AssetBundle {
    public $sourcePath = '@app/modules/kino/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
        'css/kino.css',
    ];
    public $js = [
        'js/kino.js',
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}