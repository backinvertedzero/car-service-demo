<?php

namespace app\dto;

class CarDto
{
    /**
     * @param string $title
     * @param string $description
     * @param float $price
     * @param string $photoUrl
     * @param string $contacts
     * @param CarOptionDto[]|null $options
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly float $price,
        public readonly string $photoUrl,
        public readonly string $contacts,
        public ?array $options,
    ) {
    }
}