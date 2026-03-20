<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_options}}`.
 */
class m260320_184118_create_car_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_option}}', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'brand' => $this->string()->notNull(),
            'model' => $this->string()->notNull(),
            'year' => $this->integer()->notNull(),
            'body' => $this->string()->notNull(),
            'mileage' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-car_option-car_id',
            '{{%car_option}}',
            'car_id',
            '{{%car}}',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-car_option-car_id', '{{%car_option}}');
        $this->dropTable('{{%car_option}}');
    }
}
