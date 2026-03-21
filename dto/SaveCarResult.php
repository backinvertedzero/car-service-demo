<?php

namespace app\dto;

class SaveCarResult
{
    private function __construct(
        private readonly bool $isNewCar,
        private readonly CarEntity $data
    ) {}

    public static function new(CarEntity $value): self
    {
        return new self(isNewCar: true, data: $value);
    }

    public static function found(CarEntity $value): self
    {
        return new self(isNewCar: false, data: $value);
    }

    public function isNew()
    {
        return $this->isNewCar;
    }

    public function getData(): CarEntity
    {
        return $this->data;
    }
}