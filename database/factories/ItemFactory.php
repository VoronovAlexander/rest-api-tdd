<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Item::class, function (Faker $faker) {
    return [
        'title' => $faker->realText(20),
        'content'=> $faker->realText(200),
        'is_important' => $faker->boolean()
    ];
});
