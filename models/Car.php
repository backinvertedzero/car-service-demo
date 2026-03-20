<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "car".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property float $price
 * @property string|null $photo_url
 * @property string $contacts
 * @property string|null $created_at
 *
 * @property CarOption[] $carOptions
 */
class Car extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'photo_url'], 'default', 'value' => null],
            [['title', 'price', 'contacts'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['created_at'], 'safe'],
            [['title', 'photo_url', 'contacts'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
            'photo_url' => 'Photo Url',
            'contacts' => 'Contacts',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[CarOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarOptions()
    {
        return $this->hasMany(CarOption::class, ['car_id' => 'id']);
    }

}
