<?php

namespace app\handlers\car;

use app\dto\CarDto;
use app\dto\SaveCarResult;
use app\exceptions\handlers\CarNotSaved;
use app\exceptions\repository\ModelNotSaved;

class SaveHandler extends BaseHandler
{
    /**
     * @param CarDto $carDto
     * @return SaveCarResult
     * @throws CarNotSaved
     */
    public function handle(CarDto $carDto): SaveCarResult
    {
        $result = $this->repository->findByIdempotenceKey($carDto->title, $carDto->price, $carDto->contacts);
        if ($result->isNotFound()) {
            try {
                $car = $this->repository->save($carDto);
                return SaveCarResult::new($car);
            } catch (ModelNotSaved $exception) {
                throw new CarNotSaved($exception->getMessage());
            }

        } else {
            return SaveCarResult::found($result->getData());
        }

    }
}