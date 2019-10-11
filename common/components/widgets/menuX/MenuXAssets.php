<?php

namespace common\components\widgets\menuX;
use yii\web\AssetBundle;

class MenuXAssets extends AssetBundle
{
  //  public $baseUrl = '@web/components/widgets/menuX/assets';
    public $sourcePath = '@common/components/widgets/menuX/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
       'css/menuX.css',
    ];
    public $js = [
        'js/menuX.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
