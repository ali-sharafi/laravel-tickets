<?php

namespace LaravelTickets\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelTickets\Traits\HasConfigModel;

class TicketComment extends Model
{
    use HasConfigModel;

    protected $fillable = [
        'message'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }


    public function getTable()
    {
        return config('laravel-tickets.ticket-comments-table');
    }

    /**
     * Gives the ticket that belongs to it
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Gives the creator of the message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('laravel-tickets.admin'));
    }
}
