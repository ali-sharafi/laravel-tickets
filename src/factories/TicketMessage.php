<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use LaravelTickets\Models\TicketMessage;
use Faker\Generator as Faker;
use LaravelTickets\Models\Ticket;

$factory->define(TicketMessage::class, function (Faker $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create(['user_id' => 1])->id,
        'message' => $faker->sentence
    ];
});
