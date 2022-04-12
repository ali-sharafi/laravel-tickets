<?php


namespace LaravelTickets\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Traits\HasConfigModel;

/**
 * Class Ticket
 *
 * The main data model for the ticket system
 *
 * @package LaravelTickets\Models
 */
class Ticket extends Model
{
    use HasConfigModel;

    protected $fillable = [
        'subject',
        'priority',
        'state',
        'category_id',
        'label_id'
    ];

    protected $appends = ['category_name'];

    public function getCategoryNameAttribute()
    {
        return trans('ticket_categories.' . $this->category->title);
    }

    public function getTable()
    {
        return config('laravel-tickets.database.tickets-table');
    }

    /**
     * returns every user that had sent a message in the ticket
     *
     * @param false $ticketCreatorIncluded if the ticket user should be included
     *
     * @return Collection
     */
    public function getRelatedUsers($ticketCreatorIncluded = false)
    {
        return $this
            ->messages()
            ->whereNotIn('user_id', $ticketCreatorIncluded ? [] : [$this->user_id])
            ->pluck('user_id')
            ->unique()
            ->values();
    }

    /**
     * Gives every comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Gives every message
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    /**
     * Gets the creator of the ticket,
     * can be null if the user has created ticket himself
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function opener()
    {
        return $this->belongsTo(config('laravel-tickets.admin'));
    }

    /**
     * The owner of the ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('laravel-tickets.user'));
    }

    /**
     * The category that the ticket belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * The ticket reference that the ticket binds to
     * Can be null if the user hasnt selected any reference
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function label()
    {
        return $this->belongsTo(TicketLabel::class);
    }

    /**
     * The ticket reference that the ticket binds to
     * Can be null if the user hasnt selected any reference
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reference()
    {
        return $this->hasOne(TicketReference::class);
    }

    /**
     * Gives the complete ticket activities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    /**
     * Gives the admins created messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agent()
    {
        return $this->hasOneThrough(config('laravel-tickets.admin'), TicketMessage::class, 'ticket_id', 'id', 'id', 'user_id')->where('user_type', TicketInterface::ADMIN)->orderByDesc('ticket_messages.created_at');
    }

    /**
     * Used for filtering the tickets by state
     *
     * @param $query
     * @param $state
     *
     * @return mixed
     */
    public function scopeCategory($query, $category)
    {
        if (empty($category))
            return $query;
        return $query->where('category_id', $category);
    }

    /**
     * Used for filtering the tickets by state
     *
     * @param $query
     * @param $state
     *
     * @return mixed
     */
    public function scopeLabel($query, $label)
    {
        if (empty($label))
            return $query;
        return $query->where('label_id', $label);
    }

    /**
     * Used for filtering the tickets by state
     *
     * @param $query
     * @param $state
     *
     * @return mixed
     */
    public function scopeState($query, $state)
    {
        if (empty($state))
            return $query;
        return $query->whereIn('state', is_string($state) ? [$state] : $state);
    }

    /**
     * Used for filtering the tickets by priority
     *
     * @param $query
     * @param $priority
     *
     * @return mixed
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
