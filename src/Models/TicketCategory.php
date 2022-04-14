<?php


namespace LaravelTickets\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelTickets\Traits\HasConfigModel;

/**
 * Class TicketCategory
 *
 * Used for declaring a ticket to a specific topic
 *
 * @package LaravelTickets\Models
 */
class TicketCategory extends Model
{

    use HasConfigModel;

    protected $fillable = [
        'translation'
    ];

    public function getTable()
    {
        return config('tickets.ticket-categories-table');
    }
}
