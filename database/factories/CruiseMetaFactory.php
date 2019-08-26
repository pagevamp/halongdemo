<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use App\Repos\Models\CruiseMeta;

$factory->define(CruiseMeta::class, function (Faker $faker) {
    return [
        'name' => $faker->words,
        'value' => $faker->sentence,
    ];
});
