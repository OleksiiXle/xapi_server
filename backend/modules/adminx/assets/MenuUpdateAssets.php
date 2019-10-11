<?php

namespace backend\modules\adminx\assets;

use yii\web\AssetBundle;

class MenuUpdateAssets extends AssetBundle
{
    public $sourcePath = '@app/modules/adminx/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
        'css/menuUpdate.css',
    ];
    public $js = [
        'js/menuUpdateTree.js',
        'js/menuUpdateInit.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
