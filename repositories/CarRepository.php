<?php

namespace app\repositories;

use app\dto\CarDto;
use app\dto\CarEntity;
use app\dto\FindCarResult;
use app\exceptions\repository\ModelNotSaved;
use app\mappers\CarMapper;
use app\models\Car;
use app\models\CarOption;
use Exception;
use Yii;

class CarRepository
{
    public function __construct(private readonly CarMapper $carMapper)
    {
    }

    /**
     * @param string $title
     * @param float $price
     * @param string $contacts
     * @return FindCarResult
     */
    public function findByIdempotenceKey(string $title, float $price, string $contacts): FindCarResult
    {
        $model = Car::findOne([
            'title' => $title,
            'price' => $price,
            'contacts' => $contacts
        ]);

        if (!$model) {
            return FindCarResult::notFound();
        }

        return FindCarResult::found($this->carMapper->mapToEntity($model));
    }

    /**
     * @param CarDto $dto
     * @return CarEntity
     * @throws ModelNotSaved
     */
    public function save(CarDto $dto): CarEntity
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $carModel = $this->carMapper->mapCarFromDto($dto);
            if (!$carModel->save(false)) {
                $transaction->rollBack();
                throw new ModelNotSaved("Model not saved");
            }

            $batchData = $this->carMapper->mapOptionsFromDto($carModel->id, $dto->options);
            CarOption::getDb()->createCommand()
                ->batchInsert('car_option', ['car_id', 'brand', 'model', 'year', 'body', 'mileage'], $batchData)
                ->execute();

            $transaction->commit();
            return $this->carMapper->mapToEntity($carModel);
        } catch (Exception $exception) {
            $transaction->rollBack();
            throw new ModelNotSaved($exception->getMessage());
        }
    }

}