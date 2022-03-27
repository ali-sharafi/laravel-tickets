<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use LaravelTickets\Models\TicketLabel;
use Faker\Generator as Faker;

$factory->define(TicketLabel::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'color' => $faker->colorName
    ];
});
