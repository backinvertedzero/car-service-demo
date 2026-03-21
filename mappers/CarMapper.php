<?php

namespace app\mappers;

use app\dto\CarDto;
use app\dto\CarEntity;
use app\dto\CarOptionDto;
use app\dto\CarOptionEntity;
use app\models\Car;
use app\models\CarOption;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;

class CarMapper
{
    /**
     * @param CarDto $carDto
     * @return Car
     */
    public function mapCarFromDto(CarDto $carDto): Car
    {
        $carModel = new Car();
        $carModel->title = $carDto->title;
        $carModel->price = $carDto->price;
        $carModel->photo_url = $carDto->photoUrl;
        $carModel->contacts = $carDto->contacts;
        $carModel->description = $carDto->description;
        $carModel->created_at = date('Y-m-d H:i:s');
        return $carModel;
    }

    /**
     * @param int $carId
     * @param array|null $options
     * @return array
     */
    public function mapOptionsFromDto(int $carId, ?array $options): array
    {
        if ($options == null) {
            return [];
        }

        return array_map(
            fn(CarOptionDto $opt) => [
                'car_id' => $carId,
                'brand' => $opt->brand,
                'model' => $opt->model,
                'year' => $opt->year,
                'body' => $opt->body,
                'mileage' => $opt->mileage,
            ],
            $options
        );
    }

    /**
     * @param Car $car
     * @return CarEntity
     */
    public function mapToEntity(Car $car): CarEntity
    {
        $options = array_map(
            fn(CarOption $opt) => new CarOptionEntity(
                $opt->id,
                brand: $opt->brand,
                model: $opt->model,
                year: $opt->year,
                body: $opt->body,
                mileage: $opt->mileage,
            ),
            $car->carOptions
        );

        return new CarEntity(
            id: $car->id,
            title: $car->title,
            description: $car->description,
            price: $car->price,
            photo_url: $car->photo_url,
            contacts: $car->contacts,
            created_at: $this->parseDateTime($car->created_at),
            options: $options,
        );
    }

    /**
     * @param string|null $dateString
     * @return DateTimeImmutable|null
     */
    private function parseDateTime(?string $dateString): ?DateTimeImmutable
    {
        if ($dateString === null || $dateString === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($dateString, new DateTimeZone('UTC'));
        } catch (DateMalformedStringException $exception) {
            return null;
        }
    }
}