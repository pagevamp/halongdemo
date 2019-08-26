<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Repos\Models\CruiseMeta;
use Faker\Generator as Faker;
use App\Repos\Models\Cruise;

$factory->define(Cruise::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
        'slug' => $faker->unique()->slug(5),
        'short_description' => $faker->sentence,
        'long_description' => $faker->paragraph,
        'price' => $faker->numberBetween(1, 100),
        'published' => Cruise::PUBLISHED,
        'video' => $faker->imageUrl(),
        'star' => $faker->randomElement([3, 3.5, 4, 4.5, 5]),
        'total_average_rating' => 0,
        'featured_index' => $faker->numberBetween(1, 10),
    ];
});

$factory->state(Cruise::class, 'unpublished', function (Faker $faker) {
    return [
        'published' => Cruise::UNPUBLISHED,
    ];
});
$factory->state(Cruise::class, 'published', function (Faker $faker) {
    return [
        'published' => Cruise::PUBLISHED,
    ];
});



$factory->state(Cruise::class, 'with_post_metas', function (Faker $faker) {
    return [
        'floors' => $faker->randomDigit(1, 20),
        'seo_title' => $faker->title(100),
        'seo_description' => $faker->text(100),
        'cabins' => $faker->randomDigit(1, 50),
        'guest_capacity' => $faker->randomDigit(1, 100),
        'built_year' => $faker->year(),
    ];
});

if (!function_exists('createMockedMetas')) {
    function createMockedMetas(Faker $faker)
    {
        return [
            'built_year' => $faker->year(),
            'floors' => $faker->numberBetween(1, 20),
            'cabins' => $faker->numberBetween(1, 20),
            'guest_capacity' => $faker->numberBetween(1, 20),
            'height' => $faker->numberBetween(1, 20),
            'length' => $faker->numberBetween(1, 20),
            'width' => $faker->numberBetween(1, 20),
            'boarding_time' => $faker->time(),
            'disembarking_time' => $faker->time(),
            'seo_title' => $faker->title(),
            'seo_description' => $faker->text(),
            'og_image' => $faker->imageUrl(),
            'booking_policy' => $faker->text(),
            'why_book_this_cruise' => $faker->text(),
            'seo_keywords' => $faker->text(),
            'cleanliness_rating' => 0,
            'comfort_rating' => 0,
            'staff_rating' => 0,
            'food_rating' => 0,
            'service_rating' => 0,
            'value_for_money_rating' => 0,
            'total_reviews' => 0,
        ];
    }
}

$factory->state(Cruise::class, 'create_default_metas', [])
    ->afterCreatingState(Cruise::class, 'create_default_metas', function (Cruise $cruise, Faker $faker) {
        foreach (CruiseMeta::getAllowedKeys() as $field) {
            factory(CruiseMeta::class)->create([
                'name' => $field,
                'value' => createMockedMetas($faker)[$field],
                'cruise_id' => $cruise->id,
            ]);
        }
    });


