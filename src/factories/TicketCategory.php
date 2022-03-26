<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use LaravelTickets\Models\TicketCategory;
use Faker\Generator as Faker;

$factory->define(TicketCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'desc' => $faker->sentence
    ];
});
