<?php


namespace LaravelTickets\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Traits\HasConfigModel;

/**
 * Class TicketMessage
 *
 * The message that a user sent
 *
 * @package LaravelTickets\Models
 */
class TicketMessage extends Model
{

    use HasConfigModel;

    protected $fillable = [
        'message',
        'user_type'
    ];

    protected $appends = ['admin'];

    public function getAdminAttribute()
    {
        if ($this->user_type == TicketInterface::USER) return null;
        return $this->admin()->first();
    }

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
        return config('laravel-tickets.database.ticket-messages-table');
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
        return $this->belongsTo(config('laravel-tickets.user'));
    }

    /**
     * Gives the creator of the message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(config('laravel-tickets.admin'), 'user_id');
    }

    /**
     * Gives all uploads that a made with the message
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads()
    {
        return $this->hasMany(TicketUpload::class);
    }
}
