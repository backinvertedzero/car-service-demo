<?php

namespace tests\unit\handlers;

use app\dto\CarDto;
use app\dto\CarEntity;
use app\dto\FindCarResult;
use app\dto\SaveCarResult;
use app\exceptions\handlers\CarNotSaved;
use app\exceptions\repository\ModelNotSaved;
use app\handlers\car\SaveHandler;
use app\repositories\CarRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SaveHandlerTest extends TestCase
{
    private CarRepository|MockObject $repositoryMock;
    private SaveHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(CarRepository::class);
        $this->handler = new SaveHandler($this->repositoryMock);
    }

    public function testHandleDataNotFoundAndSavedSuccessfully(): void
    {
        $carDto = new CarDto(
            title: 'BMW X5',
            description: 'Test description',
            price: 5000000.00,
            photoUrl: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            options: []
        );

        $carEntity = new CarEntity(
            id: 1,
            title: 'BMW X5',
            description: 'Test description',
            price: 5000000.00,
            photo_url: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            created_at: new \DateTimeImmutable(),
            options: []
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByIdempotenceKey')
            ->with('BMW X5', 5000000.00, '+79990000000')
            ->willReturn(FindCarResult::notFound());

        $this->repositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($carDto)
            ->willReturn($carEntity);

        $result = $this->handler->handle($carDto);

        $this->assertInstanceOf(SaveCarResult::class, $result);
        $this->assertTrue($result->isNew());
        $this->assertEquals(1, $result->getData()->id);
    }

    public function testHandleDataFoundByIdempotenceKey(): void
    {
        $carDto = new CarDto(
            title: 'BMW X5',
            description: 'Test description',
            price: 5000000.00,
            photoUrl: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            options: []
        );

        $existingCarEntity = new CarEntity(
            id: 42,
            title: 'BMW X5',
            description: 'Existing car',
            price: 5000000.00,
            photo_url: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            created_at: new \DateTimeImmutable('2023-01-01'),
            options: []
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByIdempotenceKey')
            ->with('BMW X5', 5000000.00, '+79990000000')
            ->willReturn(FindCarResult::found($existingCarEntity));

        $this->repositoryMock
            ->expects($this->never())
            ->method('save');

        $result = $this->handler->handle($carDto);

        $this->assertInstanceOf(SaveCarResult::class, $result);
        $this->assertFalse($result->isNew());
        $this->assertEquals(42, $result->getData()->id);
    }

    public function testHandleSaveThrowsModelNotSaved(): void
    {
        $carDto = new CarDto(
            title: 'BMW X5',
            description: 'Test description',
            price: 5000000.00,
            photoUrl: 'http://example.com/img.jpg',
            contacts: '+79990000000',
            options: []
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByIdempotenceKey')
            ->willReturn(FindCarResult::notFound());

        $this->repositoryMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new ModelNotSaved('Database error'));

        $this->expectException(CarNotSaved::class);
        $this->expectExceptionMessage('Database error');

        $this->handler->handle($carDto);
    }
}