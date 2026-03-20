<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "car_option".
 *
 * @property int $id
 * @property int $car_id
 * @property string $brand
 * @property string $model
 * @property int $year
 * @property string $body
 * @property int $mileage
 *
 * @property Car $car
 */
class CarOption extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['car_id', 'brand', 'model', 'year', 'body', 'mileage'], 'required'],
            [['car_id', 'year', 'mileage'], 'default', 'value' => null],
            [['car_id', 'year', 'mileage'], 'integer'],
            [['brand', 'model', 'body'], 'string', 'max' => 255],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::class, 'targetAttribute' => ['car_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_id' => 'Car ID',
            'brand' => 'Brand',
            'model' => 'Model',
            'year' => 'Year',
            'body' => 'Body',
            'mileage' => 'Mileage',
        ];
    }

    /**
     * Gets query for [[Car]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }

}
