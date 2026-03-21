<?php

namespace app\entities;

use DateTimeImmutable;

class CarEntity
{
    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param float $price
     * @param string $photo_url
     * @param string $contacts
     * @param DateTimeImmutable $created_at
     * @param array|null $options
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly float $price,
        public readonly string $photo_url,
        public readonly string $contacts,
        public readonly DateTimeImmutable $created_at,
        public readonly ?array $options = null,
    ) {
    }

}