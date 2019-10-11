<?php

use yii\db\Migration;

/**
 * Class m191006_064438_add_tbl_kino_seans
 */
class m191006_064438_add_tbl_kino_seans extends Migration
{
    const TABLE_NAME = '{{%kino_seans}}';
    const TABLE_NAME1 = '{{%kino}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'hall_id' => $this->integer(),
            'filmName' => $this->string(255)->notNull()->comment('Название фильма'),
            'cinema_hall' => $this->text()->comment('Кино-зал'),
            'data' => $this->integer(),
            'reservations_count' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_kino_kino_seans', self::TABLE_NAME,'hall_id',
            self::TABLE_NAME1, 'id', 'cascade', 'cascade');
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk_kino_kino_seans', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}