<?php

namespace LaravelTickets\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLabel extends Model
{
    protected $guarded = ['id'];

    public function getTable()
    {
        return config('laravel-tickets.ticket-labels-table');
    }
}
