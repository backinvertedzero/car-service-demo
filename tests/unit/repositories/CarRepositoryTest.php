<?php


namespace tests\unit\repositories;

use app\dto\CarDto;
use app\dto\CarOptionDto;
use app\exceptions\repository\ModelNotSaved;
use app\mappers\CarMapper;
use app\repositories\CarRepository;
use Codeception\Test\Unit;
use Yii;
use yii\db\Connection;

/**
 * @requires extension pdo_sqlite
 */
final class CarRepositoryTest extends Unit
{
    private CarMapper $mapper;
    private CarRepository $repository;
    private Connection $db;

    protected function _before(): void
    {
        parent::_before();

        $this->db = new Connection([
            'dsn' => 'sqlite::memory:',
        ]);
        $this->db->open();

        // Создаём тестовые таблицы
        $this->db->createCommand()->createTable('car', [
            'id' => 'pk',
            'title' => 'string',
            'description' => 'text',
            'price' => 'decimal(10,2)',
            'photo_url' => 'string',
            'contacts' => 'string',
            'created_at' => 'datetime',
        ])->execute();

        $this->db->createCommand()->createTable('car_option', [
            'id' => 'pk',
            'car_id' => 'integer',
            'brand' => 'string',
            'model' => 'string',
            'year' => 'integer',
            'body' => 'string',
            'mileage' => 'integer',
        ])->execute();

        Yii::$app->set('db', $this->db);

        $this->mapper = new CarMapper();
        $this->repository = new CarRepository($this->mapper);
    }

    protected function _after(): void
    {
        $this->db->close();
        parent::_after();
    }

    public function testFindByIdempotenceKeyNotFound(): void
    {
        $result = $this->repository->findByIdempotenceKey(
            title: 'Nonexistent Car',
            price: 1000000.00,
            contacts: '+79990000000'
        );

        $this->assertTrue($result->isNotFound());
        $this->assertFalse($result->isFound());
        $this->assertNull($result->getData());
    }

    public function testFindByIdempotenceKeyFound(): void
    {
        // Создаём тестовую запись
        $this->db->createCommand()->insert('car', [
            'title' => 'BMW X5',
            'price' => 5000000.00,
            'contacts' => '+79990000000',
            'description' => 'Test car',
            'photo_url' => 'http://example.com/img.jpg',
            'created_at' => date('Y-m-d H:i:s'),
        ])->execute();

        $result = $this->repository->findByIdempotenceKey(
            title: 'BMW X5',
            price: 5000000.00,
            contacts: '+79990000000'
        );

        $this->assertTrue($result->isFound());
        $this->assertFalse($result->isNotFound());
        $this->assertNotNull($result->getData());
        $this->assertEquals('BMW X5', $result->getData()->title);
    }

    public function testSaveSuccessWithoutOptions(): void
    {
        $carDto = new CarDto(
            title: 'Audi A6',
            description: 'New car',
            price: 3000000.00,
            photoUrl: 'http://example.com/audi.jpg',
            contacts: '+79991112233',
            options: null
        );

        $entity = $this->repository->save($carDto);

        $this->assertNotNull($entity->id);
        $this->assertEquals('Audi A6', $entity->title);
        $this->assertEquals(3000000.00, $entity->price);
        $this->assertNotNull($entity->created_at);
    }

    public function testSaveSuccessWithOptions(): void
    {
        $carDto = new CarDto(
            title: 'Mercedes E-Class',
            description: 'Luxury car',
            price: 4500000.00,
            photoUrl: 'http://example.com/merc.jpg',
            contacts: '+79995556677',
            options: [
                new CarOptionDto(
                    brand: 'Mercedes',
                    model: 'E-Class',
                    year: 2022,
                    body: 'Sedan',
                    mileage: 15000
                ),
                new CarOptionDto(
                    brand: 'Mercedes',
                    model: 'E-Class AMG',
                    year: 2023,
                    body: 'Sedan',
                    mileage: 5000
                ),
            ]
        );

        $entity = $this->repository->save($carDto);

        $this->assertNotNull($entity->id);
        $this->assertCount(2, $entity->options);
        $this->assertEquals('Mercedes', $entity->options[0]->brand);
        $this->assertEquals(2023, $entity->options[1]->year);
    }

}