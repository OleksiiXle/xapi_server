<?php

namespace common\components\widgets\menuG\models;


class MenuG extends \yii\db\ActiveRecord{
    //-- $query->createCommand()->getSql()

    private $_parentsCount;

    /**
     * Возаращает количество прямых предков
     */
    public function getParentsCount()
    {
        $parentsCount=0;
        $pid = $this->parent_id;
        do{
            $parent = self::findOne($pid);
            if (isset($parent)){
                $parentsCount++;
                $pid = $parent->parent_id;
            }
        } while($parent != null);
        $this->_parentsCount = $parentsCount;
        return $this->_parentsCount;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'name', 'route', 'role'], 'required'],
            [['name', 'route', 'role'], 'string', 'min' => 3, 'max' => 255],
            [['parent_id', 'sort'], 'integer'],

            [['node1' , 'node2' , 'nodeAction'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'parent_id' => 'Parent ID',
            'sort' => 'Код черговості',
            'name' => 'Найменування',
            'route' => 'Маршрут',
            'role' => 'Роль',
        ];
    }


    public function getChildren() {
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }

    //*****************************************    ПЕРЕОПРЕДЕЛЕННЫЕ МЕТОДЫ   ***************************
    public function beforeSave($insert) {
        if ($insert){
            //-- определение сортировки
            $maxSort = self::find()->where(['parent_id' => $this->parent_id])->max('sort');
            $this->sort = (isset($maxSort)) ? ($maxSort +1) : 1;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * Возаращает массив прямых потомков для вывода их при раскрытии узла дерева
     * @param int $parent_id
     * @param null $level
     * @return array
     */
    public static function getMenuArray($parent_id = 0) {
        $res = [];
        $tree = self::find()->andWhere(['parent_id' => $parent_id])->orderBy('sort')->all();
        foreach ($tree as $d){
            $res[] = [
                'id'            => $d->id,
                'parent_id'     => $d->parent_id,
                'name'          => $d->name,
                'hasChildren'   => (count($d->children) > 0),
            ];
        }
        return $res;
    }

    /**
     * Возаращает массив идентификаторов предков плюс свой ид
     * @param $id
     * @return array
     */
    public static function getParentsIds($id){
        $parents=[$id];
        $node = self::findOne($id);
        if (isset($node)){
            $pid = $node->parent_id;
            do{
                $parent = self::findOne($pid);
                if (isset($parent)){
                    $parents[] = $parent->id;
                    $pid = $parent->parent_id;
                }
            } while($parent != null);
        }
        return $parents;
    }

    /**
     * Записывает в массив $target идентификаторы всех потомков
     * @param $parent_id
     * @param $target
     * @return bool
     */
    public static function getChildrenArray($parent_id, &$target)
    {
        //--
        $children = self::find()
            ->where(['parent_id' => $parent_id])
        //    ->asArray()
            ->all();
        if (count($children) > 0) {
            foreach ($children as $child) {
                $target[]=  [
                    'id' => $child['id'],
                    'parent_id' => $child['parent_id'],
                    'name' => $child['name'],
                    'hasChildren'   => (count($child->children) > 0),
                ];
                self::getChildrenArray($child['id'], $target);
            }
            return true;
        }
    }



    /**
     * Возвращает строку с ид потомков
     * @param $tree - массив дерева типа self::find()->asArray()->all();
     * @param $parent_id - родительский элемент
     * @return string - строка через запятую с ИД его потомков
     */
    public static function getAllChildren($tree, $parent_id){
        $html = '';
        foreach ($tree as $row){
            if ($row['parent_id'] == $parent_id) {
                $html .= $row['id'] ;
                $html .=  ', ' . self::getAllChildren($tree, $row['id']);
            }
        }
        return $html;
    }


    /**
     * +++ Возвращает строку с деревом
     * @param $tree - полный массив дерева
     * @param $pid - корень
     * @return string
     */
    public static function getTree($tree, $pid, $gpid=7){
        $html = '';
        foreach ($tree as $row) {
            if ($row['parent_id'] == $pid) {
                $hasChildren = self::find()->where(['parent_id' => $row['id']])->count();
                if ($hasChildren){
                    $content = '<a class="node" >' . \Yii::t('app', $row['name'])  . '</a>';
                } else {
                    $content = '<a class="route" href="'. $row['route'] . '">' . \Yii::t('app', $row['name']) . '</a>';
                }
                if ($pid == 0){
                    $html .= '<li class="menu-tops menu-item" data-id="' . $row['id'] . '" data-mode="close">'
                        . $content
                        . self::getTree($tree, $row['id'], $row['parent_id'])
                        . '</li>';

                } else {
                    $html .= '<li class="menu-item" data-id="' . $row['id'] . '" data-mode="close">'
                        . $content
                        . self::getTree($tree, $row['id'], $row['parent_id'])
                        . '</li>';
                }
            }
        }
        $ulClass = ($pid > 0) ? 'submenu' : '';
        $ulClass .= ($gpid == 0) ? ' firstLevelChildren' : '';
        $ulClass .= ($pid > 0) ? " childrenNoActive" : '';
        return $html ? '<ul class=" ' . $ulClass . '"  >' . $html . '</ul>' : '';
    //    return $html ? '<ul class=" ' . $ulClass . '" style="padding-left: 15px " >' . $html . '</ul>' : '';
    }











    public static function getTree___($tree, $pid){
        $html = '';
        foreach ($tree as $row) {
            if ($row['parent_id'] == $pid) {
                if ($pid > -1){
                    $hasChildren = self::find()->where(['parent_id' => $row['id']])->count();
                    $parent = self::find()->where(['id' => $row['parent_id']])->asArray()->one();
                    if ($hasChildren){
                        $content = '<a class="node" >' . $row['name'] . '</a>';
                    } else {
                        $content = '<a class="route" href="'. $row['route'] . '">' . $row['name'] . '</a>';
                    }
                    if ($parent['id'] == 0){
                        $html .= '<li class="menu-tops menu-item" data-id="' . $row['id'] . '" data-mode="close">'
                            . $content
                            . self::getTree($tree, $row['id'])
                            . '</li>';

                    } else {
                        $html .= '<li class="menu-item" data-id="' . $row['id'] . '" data-mode="close">'
                            . $content
                            . self::getTree($tree, $row['id'])
                            . '</li>';
                    }

                } else{
                    $html .= self::getTree($tree, $row['id']);

                }
            }
        }
        $ulClass = ($pid > 0) ? 'submenu' : '';
        return $html ? '<ul class="' . $ulClass . '" data-parent_id="' . $pid . '" >' . $html . '</ul>' : '';
    }

    /**
     * Записывает в массив $target идентификаторы всех потомков
     * @param $parent_id
     * @param $target
     * @return bool
     */
    public static function getIds($parent_id, &$target)
    {
        //--
        $children = self::find()
            ->where(['parent_id' => $parent_id])
            ->asArray()
            ->all();
        if (count($children) > 0) {
            foreach ($children as $child) {
                $target[]=  [
                    'id' =>  $child['id'],
                    'parent_id' =>  $child['parent_id'],
                    'name' =>  $child['name'],
                ];
                self::getIds($child['id'], $target);
            }
            return true;
        }
    }

    public static function getHorizontalMenu()
    {
        $allItems = (new \yii\db\Query)
            ->select('id, parent_id, name, route')
            ->from("menu_x")
            ->where(['!=', 'parent_id', 0])
            ->all();
        ;
        $parentsChildren = [];
        foreach ($allItems as $item){
            $children = (new \yii\db\Query)
                ->select('id, parent_id, name, route')
                ->from("menu_x")
                ->where(['parent_id'=> $item['id']])
                ->orderBy('sort')
                ->all();
            $parentsChildren[$item['id']] = [
                'item' => $item,
                'children' => $children,
            ]
            ;
        }


        $roots = (new \yii\db\Query)
            ->select("ch.id AS id, ch.parent_id AS parent_id, ch.name AS name, ch.route AS route")
            ->from("menu_x ch")
            ->innerJoin("menu_x par", "ch.parent_id = par.id")
            ->where(['par.parent_id' => 0])
            ->orderBy('par.sort, ch.sort')
            ->all();
        return $parentsChildren;
    }

}
