<?php

namespace tests\unit\dto;

use app\dto\CarDto;
use app\dto\CarOptionDto;
use Codeception\Test\Unit;

final class CarDtoTest extends Unit
{
    public function testCarDtoCreation(): void
    {
        $dto = new CarDto(
            title: 'BMW X5',
            description: 'Test description',
            price: 5000000.00,
            photoUrl: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            options: [
                new CarOptionDto(
                    brand: 'BMW',
                    model: 'X5',
                    year: 2020,
                    body: 'SUV',
                    mileage: 45000
                )
            ]
        );

        $this->assertEquals('BMW X5', $dto->title);
        $this->assertEquals(5000000.00, $dto->price);
        $this->assertEquals('+79990000000', $dto->contacts);
        $this->assertEquals('Test description', $dto->description);
        $this->assertEquals('http://example.com/img.jpg', $dto->photoUrl);
        $this->assertCount(1, $dto->options);
    }

    public function testCarDtoWithEmptyOptions(): void
    {
        $dto = new CarDto(
            title: 'Audi A6',
            description: '',
            price: 3000000.00,
            photoUrl: '',
            contacts: '+79991112233',
            options: null
        );

        $this->assertNull($dto->options);
    }

    public function testCarOptionDtoCreation(): void
    {
        $optionDto = new CarOptionDto(
            brand: 'Mercedes',
            model: 'E-Class',
            year: 2022,
            body: 'Sedan',
            mileage: 15000
        );

        $this->assertEquals('Mercedes', $optionDto->brand);
        $this->assertEquals('E-Class', $optionDto->model);
        $this->assertEquals(2022, $optionDto->year);
        $this->assertEquals('Sedan', $optionDto->body);
        $this->assertEquals(15000, $optionDto->mileage);
    }
}