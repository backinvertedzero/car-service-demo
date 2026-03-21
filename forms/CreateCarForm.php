<?php

declare(strict_types=1);

namespace app\forms;

use app\dto\CarDto;
use app\dto\CarOptionDto;
use yii\base\Model;

class CreateCarForm extends Model
{
    public string $title = '';
    public string $description = '';
    public string $price = '';
    public string $photo_url = '';
    public string $contacts = '';
    public ?array $options = null;

    public function rules(): array
    {
        return [
            [['title', 'price', 'contacts', 'photo_url', 'description'], 'required'],
            [['title', 'contacts', 'photo_url'], 'string', 'max' => 255],
            [['price'], 'number'],
            [['description'], 'string'],
            [['options'], 'safe'],
            [['options'], 'validateOptions'],
        ];
    }

    public function validateOptions($attribute, $params): void
    {
        if ($this->$attribute === null) {
            return;
        }

        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'Options must be an array or null.');
            return;
        }

        $requiredFields = ['brand', 'model', 'year', 'body', 'mileage'];

        foreach ($this->$attribute as $index => $opt) {
            if (!is_array($opt)) {
                $this->addError($attribute, "Option item #$index must be an object.");
                continue;
            }
            foreach ($requiredFields as $field) {
                if (!isset($opt[$field])) {
                    $this->addError($attribute, "Option item #$index missing field: $field");
                }
            }
        }
    }

    public function makeCarDto(): CarDto
    {
        $options = null;
        if ($this->options !== null && count($this->options) > 0) {
            foreach ($this->options as $item) {
                $options[] = new CarOptionDto(
                    brand: $item['brand'],
                    model: $item['model'],
                    year: $item['year'],
                    body: $item['body'],
                    mileage: $item['mileage']
                );
            }
        }

        return new CarDto(
            title: $this->title,
            description: $this->description,
            price: (float)$this->price,
            photoUrl: $this->photo_url,
            contacts: $this->contacts,
            options: $options
        );
    }
}