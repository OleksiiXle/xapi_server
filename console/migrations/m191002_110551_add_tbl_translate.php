<?php

use yii\db\Migration;

/**
 * Class m191002_110551_add_tbl_translate
 */
class m191002_110551_add_tbl_translate extends Migration
{
    const TABLE_ = '{{%translation}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_, [
            'id' => $this->primaryKey(),
            'tkey' => $this->integer(11)->notNull()->defaultValue(0),
            'category' => $this->string(3)->defaultValue('app'),
            'language' => $this->string(10)->notNull(),
            'message' => $this->text()->defaultValue(null),
            'links' => $this->string(250)->defaultValue(null),
        ], $tableOptions);
    }


    public function safeDown()
    {
        $this->dropTable(self::TABLE_);

        return false;
    }

}