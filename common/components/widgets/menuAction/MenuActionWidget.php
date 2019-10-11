<?php

namespace common\components\widgets\menuAction;

use yii\base\Widget;

class MenuActionWidget extends Widget
{
    public $icon = "glyphicon glyphicon-list";
    public $items = [
        'text' => 'route',
    ];
    public $offset = 0;
    public $method = '';


    public function run()
    {

        return $this->render('menuAction',
            [
                'icon' => $this->icon,
                'items' => $this->items,
                'offset' => $this->offset,
                'method' => (!empty($this->method)) ? 'data-method=' . $this->method : '',
            ]);
    }

}
