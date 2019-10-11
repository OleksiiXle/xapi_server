<?php

namespace backend\modules\adminx\assets;

use yii\web\AssetBundle;

class AdminxUpdateAuthItemAsset extends  AssetBundle {
  //  public $baseUrl = '@web/modules/adminx/assets';
    public $sourcePath = '@app/modules/adminx/assets';
    public $publishOptions = ['forceCopy' => true];
    public $css = [
        'css/adminx.css',
    ];
    public $js = [
        'js/updateAuthItem.js',
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
    public $depends = [
    ];
}