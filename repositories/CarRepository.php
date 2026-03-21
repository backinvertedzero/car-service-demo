<?php

namespace app\repositories;

use app\dto\CarDto;
use app\dto\CarEntity;
use app\dto\FindCarResult;
use app\exceptions\repository\ModelNotFound;
use app\exceptions\repository\ModelNotSaved;
use app\mappers\CarMapper;
use app\models\Car;
use app\models\CarOption;
use Exception;
use Yii;
use yii\data\Pagination;

readonly class CarRepository
{
    public function __construct(private CarMapper $carMapper)
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

    /**
     * @param int $id
     * @return CarEntity
     * @throws ModelNotFound
     */
    public function getById(int $id): CarEntity
    {
        $model = Car::findOne($id);

        if ($model == null) {
            throw new ModelNotFound('Car not found by id ' . $id);
        }

        return $this->carMapper->mapToEntity($model);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findAll(int $page, int $perPage = 10): array
    {
        $query = Car::find()->orderBy(['created_at' => SORT_DESC]);

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $perPage,
            'page' => $page - 1,
            'validatePage' => true,
        ]);

        $models = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $entities = [];

        // @todo backlog!
        foreach ($models as $model) {
            $entities[] = $this->carMapper->mapToEntity($model);
        }

        return $entities;
    }

}