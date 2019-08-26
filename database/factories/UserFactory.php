<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Repos\Models\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'username' => str_slug($faker->unique()->words(4, true)),
        'avatar' => $faker->imageUrl,
        'role' => User::ADMIN,
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'email_verified_at' => now(),
        'password' => 'secret',
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'admin', function ($user) {
    return [
        'role' => User::ADMIN,
    ];
});

$factory->state(User::class, 'should_verify_email', function ($user) {
    return [
        'should_verify_email' => true,
    ];
});

$factory->state(User::class, 'agent', function ($user) {
    return [
        'role' => User::AGENT,
    ];
});

$factory->state(User::class, 'client', function ($user) {
    return [
        'role' => User::CLIENT,
    ];
});
