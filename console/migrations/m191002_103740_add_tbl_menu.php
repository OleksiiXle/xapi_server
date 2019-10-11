<?php

use yii\db\Migration;

/**
 * Class m191002_103740_add_tbl_menu
 */
class m191002_103740_add_tbl_menu extends Migration
{
    const TABLE_NAME = '{{%menu}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->comment('Идентификатор'),
            'parent_id' => $this->integer(11)->notNull(),
            'sort' => $this->integer(11)->defaultValue(0),
            'name' => $this->string(255)->defaultValue(null)->comment('Название'),
            'route' => $this->string(255)->defaultValue(null)->comment('Маршрут'),
            'role' => $this->string(255)->defaultValue(null)->comment('Роль'),
            'access_level' => $this->integer()->defaultValue(0)->comment('Уровень пользователя')
        ], $tableOptions);
        $this->createIndex('parent_id', self::TABLE_NAME, 'parent_id');
    }


    public function safeDown()
    {
        $this->dropIndex('parent_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}