<?php

namespace App\Repos\Models;

use App\Repos\Traits\Filterable;

class CruiseMeta extends AbstractModel
{
    use Filterable;

    private static $allowedKeys = [
        'built_year',
        'floors',
        'cabins',
        'guest_capacity',
        'height',
        'length',
        'width',
        'boarding_time',
        'disembarking_time',
        'seo_title',
        'og_image',
        'booking_policy',
        'why_book_this_cruise',
        'seo_keywords',
        'seo_description',
        'cleanliness_rating',
        'comfort_rating',
        'staff_rating',
        'food_rating',
        'service_rating',
        'value_for_money_rating',
        'total_reviews',
    ];

    public function generateRow($key, $value, $cruiseId)
    {
        return [
            'name' => $key,
            'value' => $value,
            'cruise_id' => $cruiseId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function create(Cruise $cruise, array $data)
    {
        $data = $this->filterFields($data);

        if ($data) {
            $this->whereIn('name', array_keys($data))->where('cruise_id', $cruise->getId())->delete();
            $rows = [];
            foreach ($data as $key => $value) {
                $rows[] = $this->generateRow($key, $value, $cruise->getId());
            }

            $this->insert($rows);
        }
    }

    public function getKey()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function getAllowedKeys(): array
    {
        return self::$allowedKeys;
    }

    private function filterFields($data)
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (\in_array($key, self::$allowedKeys)) {
                if ('array' === \gettype($value)) {
                    $value = json_encode($value);
                }
                $filteredData[$key] = $value;
            }
        }

        return $filteredData;
    }
}
