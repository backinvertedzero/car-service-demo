## Проект
Разработать REST API сервис для управления объявлениями автомобилей с использованием PHP8, Yii2 и PostgreSQL.
Код должен быть организован по многослойной архитектуре с использованием паттернов Service, Repository, Entity, DataMapper и следовать принципам SOLID. Использовать Dependency Injection для управления зависимостями.

## Предусловия
Используется контейнер на основе yiisoftware/yii2-php:8.4-apache, каких-то дополнительных библиотек нет. В 
дополнение к нему добавил контейнер postgres.
Поэтому достаточно запустить проект и выполнить миграции:
- git clone git@github.com:backinvertedzero/car-service-demo.git
- cd car-service-demo
- docker compose up -d
- войти в контейнер php и в нем выполнить yii migrate

## Создание сущности
curl --location 'http://localhost:8000/car/create' \
--header 'Content-Type: application/json' \
--data '{
"title": "BMW X5",
"description": "Отличное состояние",
"price": "777",
"photo_url": "http://example.com/img.jpg",
"contacts": "+79990000000",
"options": [{
"brand": "BMW",
"model": "X5",
"year": 2020,
"body": "SUV",
"mileage": 45000
}]
}'

В целом запрос сделал идемпотентным. Хотя в задаче об этом не сказано. Также какие-то функциональные требования, 
относительно полей, не приведены, поэтому тоже в общем-то ничего с ними не делал. Выяснять требования у hr менеджера 
в выходные не стал, сам выдумывать тоже.

## Получение сущности
curl --location 'http://localhost:8000/car/1' 

## Список сущностей
curl --location 'http://localhost:8000/car/list?page=1'
В целом выводится по 10 штук. Зашил в коде. Для демо примера. Обычно так не делаю - прописываю в конфиге.

## Тесты
Выполняются внутри контейнера
- vendor/bin/codecept run unit dto/CarDtoTest
- vendor/bin/codecept run unit models/CreateCarFormTest
- vendor/bin/codecept run unit repositories/CarRepositoryTest
- endor/bin/codecept run unit handlers/SaveHandlerTest

## Summary
В целом сделал так как счел нужным, без обновления знаний и чтения документации по yii2, поэтому какие-то моменты 
мог упустить. Особенно с обновлением моделей после создания. Не помню, есть ли это в yii2. Сущности в основном у 
меня это разновидность dto, поэтому поместил их в такой неймспейс. Так-то entity это понятие из совершенно иного 
мира, особо не связанного с AR. Поэтому в данном примере entity - это дто, только представляющая конкретную 
существующую сущность из бд. Тоже самое и с репозиториями. Да, подобное часто называют репозиториями, но... 
В целом получилось то, что есть. Спасибо за интересный пример.
