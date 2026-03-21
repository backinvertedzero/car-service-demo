<?php

namespace app\entities;

class CarOptionEntity
{
    /**
     * @param int $id
     * @param string $brand
     * @param string $model
     * @param int $year
     * @param string $body
     * @param int $mileage
     */
    public function __construct(
        public readonly int $id,
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly string $body,
        public readonly int $mileage
    ) {
    }
}