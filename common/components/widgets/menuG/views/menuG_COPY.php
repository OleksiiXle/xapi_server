<?php
use \yii\helpers\Html;
?>

<?php
//echo $html;
//echo var_dump($tree);
$_tree = \yii\helpers\Json::htmlEncode($tree);
$this->registerJs("
    var _tree = {$_tree};
",\yii\web\View::POS_HEAD);
\app\components\widgets\menuG\MenuGAssets::register($this);
?>

<div class="container-fluid">
    <div class="row">
        <li class="menu-tops menu-item" data-id="1" data-mode="close"><a >Menu1</a>
            <ul class="submenu" data-parent_id="1" >
                <li class="menu-item" data-id="11" data-mode="close"><a>submenu 1111</a></li>
                <li class="menu-item" data-id="12" data-mode="close"><a>submenu 1113</a>
                    <ul  class="submenu" data-parent_id="12" >
                        <li class="menu-item" data-id="111" data-mode="close"><a>submenu 2221</a> </li>
                        <li class="menu-item" data-id="112" data-mode="close">submenu 2223</li>
                    </ul>

                </li>
                <li class="menu-item" data-id="12" data-mode="close" >submenu 2223</li>
            </ul>
        </li>
        <li class="menu-tops menu-item"><a>Menu2</a>
            <ul>
                <li class="submenu">submenu 1111</li>
                <li class="submenu">submenu 1112</li>
                <li class="submenu">submenu 1113</li>
            </ul>
        </li>
        <li class="menu-tops menu-item"><a>Menu3 </a>
            <ul>
                <li class="submenu menu-item">submenu 1111</li>
                <li class="submenu menu-item">submenu 1112</li>
                <li class="submenu menu-item">submenu 1113</li>
            </ul>
        </li>

    </div>

    <div class="row" style="background-color: aliceblue">
        <?=$html;?>
    </div>
</div>
<script>
</script>
