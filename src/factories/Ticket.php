<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use LaravelTickets\Models\Ticket;
use Faker\Generator as Faker;
use LaravelTickets\Models\TicketCategory;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'subject' => $faker->text,
        'priority' => $faker->randomElement(config('laravel-tickets.priorities')),
        'message' => $faker->text,
        'files' => $faker->image(),
        'category_id' => factory(TicketCategory::class)->create()->id
    ];
});