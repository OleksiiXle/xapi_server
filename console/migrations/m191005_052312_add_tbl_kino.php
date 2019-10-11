<?php

use yii\db\Migration;

/**
 * Class m191005_052312_add_tbl_kino
 */
class m191005_052312_add_tbl_kino extends Migration
{
    const TABLE_NAME = '{{%kino}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->defaultValue(null)->comment('Название'),
            'cinema_hall' => $this->text()->comment('Кино-зал'),
            'data' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }


    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}