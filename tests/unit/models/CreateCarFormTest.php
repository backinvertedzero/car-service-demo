<?php

namespace tests\unit\models;

use app\forms\CreateCarForm;
use app\dto\CarDto;
use app\dto\CarOptionDto;
use Codeception\Test\Unit;

final class CreateCarFormTest extends Unit
{
    private CreateCarForm $form;

    protected function _before(): void
    {
        parent::_before();
        $this->form = new CreateCarForm();
    }

    public function testValidateSuccessWithAllFields(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Отличное состояние',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'model' => 'X5',
                    'year' => 2020,
                    'body' => 'SUV',
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->getErrors());
    }

    public function testValidateSuccessWithoutOptions(): void
    {
        $this->form->attributes = [
            'title' => 'Audi A6',
            'description' => 'Седан бизнес класса',
            'price' => 3000000.00,
            'photo_url' => 'http://example.com/audi.jpg',
            'contacts' => '+79991112233',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->getErrors());
    }

    public function testValidateSuccessWithEmptyOptionsArray(): void
    {
        $this->form->attributes = [
            'title' => 'Mercedes E-Class',
            'description' => 'Luxury car',
            'price' => 4500000.00,
            'photo_url' => 'http://example.com/merc.jpg',
            'contacts' => '+79995556677',
            'options' => [],
        ];

        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->getErrors());
    }

    public function testValidateSuccessWithMultipleOptions(): void
    {
        $this->form->attributes = [
            'title' => 'Toyota Camry',
            'description' => 'Надёжный автомобиль',
            'price' => 2500000.00,
            'photo_url' => 'http://example.com/toyota.jpg',
            'contacts' => '+79998889900',
            'options' => [
                [
                    'brand' => 'Toyota',
                    'model' => 'Camry',
                    'year' => 2021,
                    'body' => 'Sedan',
                    'mileage' => 30000,
                ],
                [
                    'brand' => 'Toyota',
                    'model' => 'Camry SE',
                    'year' => 2022,
                    'body' => 'Sedan',
                    'mileage' => 15000,
                ],
            ],
        ];

        $this->assertTrue($this->form->validate());
        $this->assertCount(2, $this->form->options);
    }

    public function testValidateFailMissingTitle(): void
    {
        $this->form->attributes = [
            'title' => '',
            'description' => 'Test',
            'price' => 1000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('title', $this->form->getErrors());
    }

    public function testValidateFailMissingDescription(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => '',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testValidateFailMissingPrice(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 0.0,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testValidateFailMissingContacts(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '',
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('contacts', $this->form->getErrors());
    }

    public function testValidateFailMissingPhotoUrl(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => '',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testValidateFailMissingOptions(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testValidateFailTitleTooLong(): void
    {
        $this->form->attributes = [
            'title' => str_repeat('a', 256),
            'description' => 'Test',
            'price' => 1000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('title', $this->form->getErrors());
    }

    public function testValidateFailContactsTooLong(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 1000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => str_repeat('c', 256),
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('contacts', $this->form->getErrors());
    }

    public function testValidateFailPhotoUrlTooLong(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 1000000.00,
            'photo_url' => str_repeat('u', 256),
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('photo_url', $this->form->getErrors());
    }

    public function testValidateFailPriceNotNumeric(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 'not-a-number',
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('price', $this->form->getErrors());
    }

    public function testValidateFailDescriptionNotString(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 12345,
            'price' => 1000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
        $this->assertEquals('12345', $this->form->description);
    }

    public function testValidateOptionsMissingBrandField(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'model' => 'X5',
                    'year' => 2020,
                    'body' => 'SUV',
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
        $this->assertStringContainsString('missing field: brand', $this->form->getFirstError('options'));
    }

    public function testValidateOptionsMissingModelField(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'year' => 2020,
                    'body' => 'SUV',
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
        $this->assertStringContainsString('missing field: model', $this->form->getFirstError('options'));
    }

    public function testValidateOptionsMissingYearField(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'model' => 'X5',
                    'body' => 'SUV',
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
        $this->assertStringContainsString('missing field: year', $this->form->getFirstError('options'));
    }

    public function testValidateOptionsMissingBodyField(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'model' => 'X5',
                    'year' => 2020,
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
        $this->assertStringContainsString('missing field: body', $this->form->getFirstError('options'));
    }

    public function testValidateOptionsMissingMileageField(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'model' => 'X5',
                    'year' => 2020,
                    'body' => 'SUV',
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
        $this->assertStringContainsString('missing field: mileage', $this->form->getFirstError('options'));
    }

    public function testValidateOptionsMultipleErrors(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                ],
                [
                ],
            ],
        ];

        $this->assertFalse($this->form->validate());
        $this->assertArrayHasKey('options', $this->form->getErrors());
    }

    public function testMakeCarDtoWithValidData(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Отличное состояние',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => [
                [
                    'brand' => 'BMW',
                    'model' => 'X5',
                    'year' => 2020,
                    'body' => 'SUV',
                    'mileage' => 45000,
                ],
            ],
        ];

        $this->assertTrue($this->form->validate());

        $dto = $this->form->makeCarDto();

        $this->assertInstanceOf(CarDto::class, $dto);
        $this->assertEquals('BMW X5', $dto->title);
        $this->assertEquals('Отличное состояние', $dto->description);
        $this->assertEquals(5000000.00, $dto->price);
        $this->assertEquals('http://example.com/img.jpg', $dto->photoUrl);
        $this->assertEquals('+79990000000', $dto->contacts);
        $this->assertIsArray($dto->options);
        $this->assertCount(1, $dto->options);
        $this->assertInstanceOf(CarOptionDto::class, $dto->options[0]);
        $this->assertEquals('BMW', $dto->options[0]->brand);
        $this->assertEquals('X5', $dto->options[0]->model);
        $this->assertEquals(2020, $dto->options[0]->year);
        $this->assertEquals('SUV', $dto->options[0]->body);
        $this->assertEquals(45000, $dto->options[0]->mileage);
    }

    public function testMakeCarDtoWithoutOptions(): void
    {
        $this->form->attributes = [
            'title' => 'Audi A6',
            'description' => 'Седан',
            'price' => 3000000.00,
            'photo_url' => 'http://example.com/audi.jpg',
            'contacts' => '+79991112233',
            'options' => [],
        ];

        $this->assertTrue($this->form->validate());

        $dto = $this->form->makeCarDto();

        $this->assertInstanceOf(CarDto::class, $dto);
        $this->assertNull($dto->options);
    }

    public function testMakeCarDtoWithMultipleOptions(): void
    {
        $this->form->attributes = [
            'title' => 'Toyota Camry',
            'description' => 'Надёжный',
            'price' => 2500000.00,
            'photo_url' => 'http://example.com/toyota.jpg',
            'contacts' => '+79998889900',
            'options' => [
                [
                    'brand' => 'Toyota',
                    'model' => 'Camry',
                    'year' => 2021,
                    'body' => 'Sedan',
                    'mileage' => 30000,
                ],
                [
                    'brand' => 'Toyota',
                    'model' => 'Camry SE',
                    'year' => 2022,
                    'body' => 'Sedan',
                    'mileage' => 15000,
                ],
            ],
        ];

        $this->assertTrue($this->form->validate());

        $dto = $this->form->makeCarDto();

        $this->assertCount(2, $dto->options);
        $this->assertEquals('Camry', $dto->options[0]->model);
        $this->assertEquals('Camry SE', $dto->options[1]->model);
        $this->assertEquals(2021, $dto->options[0]->year);
        $this->assertEquals(2022, $dto->options[1]->year);
    }

    public function testMakeCarDtoPriceTypeConversion(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => '5000000',
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());

        $dto = $this->form->makeCarDto();

        $this->assertEquals(5000000.00, $dto->price);
        $this->assertIsFloat($dto->price);
    }

    public function testValidateWithZeroPrice(): void
    {
        // А потому что функциональных требований нет по сути. откуда мне знать, можно или нет с такой ценой
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 0.0,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testValidateWithEmptyStringDescription(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => '',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertTrue($this->form->validate());
    }

    public function testFormAttributesGetters(): void
    {
        $this->form->attributes = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->assertEquals('BMW X5', $this->form->title);
        $this->assertEquals('Test', $this->form->description);
        $this->assertEquals(5000000.00, $this->form->price);
        $this->assertEquals('http://example.com/img.jpg', $this->form->photo_url);
        $this->assertEquals('+79990000000', $this->form->contacts);
        $this->assertNull($this->form->options);
    }

    public function testFormLoadFromArray(): void
    {
        $data = [
            'CreateCarForm' => [
                'title' => 'BMW X5',
                'description' => 'Test',
                'price' => 5000000.00,
                'photo_url' => 'http://example.com/img.jpg',
                'contacts' => '+79990000000',
                'options' => null,
            ],
        ];

        $this->form->load($data);

        $this->assertEquals('BMW X5', $this->form->title);
        $this->assertEquals(5000000.00, $this->form->price);
    }

    public function testFormLoadFromRawArray(): void
    {
        $data = [
            'title' => 'BMW X5',
            'description' => 'Test',
            'price' => 5000000.00,
            'photo_url' => 'http://example.com/img.jpg',
            'contacts' => '+79990000000',
            'options' => null,
        ];

        $this->form->load($data, '');

        $this->assertEquals('BMW X5', $this->form->title);
        $this->assertEquals(5000000.00, $this->form->price);
    }
}