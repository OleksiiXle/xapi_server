<?php

namespace common\components\widgets\menuG;

use yii\web\AssetBundle;

class MenuGAssets extends AssetBundle
{
    public $baseUrl = '@web/components/widgets/menuG/assets';
    public $sourcePath = '@app/components/widgets/menuG/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
        'css/menuG.css',

    ];
    public $js = [
        'js/menuG.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
