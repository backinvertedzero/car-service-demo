<?php

namespace app\dto;

class FindCarResult
{
    private function __construct(
        private readonly bool $found,
        private readonly ?CarEntity $data = null
    ) {}

    public static function found(CarEntity $value): self
    {
        return new self(found: true, data: $value);
    }

    public static function notFound(): self
    {
        return new self(found: false);
    }

    public function isFound(): bool
    {
        return $this->found;
    }

    public function isNotFound(): bool
    {
        return !$this->found;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

}