<?php


namespace LaravelTickets\Observers;


use Ramsey\Uuid\Uuid;
use LaravelTickets\Events\TicketCloseEvent;
use LaravelTickets\Events\TicketOpenEvent;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketActivity;
use LaravelTickets\Models\TicketMessage;

class TicketObserver
{

    public function created(Ticket $ticket)
    {
        $ticketActivity = new TicketActivity([ 'type' => 'CREATE' ]);
        $ticketActivity->ticket()->associate($ticket);
        $ticketActivity->targetable()->associate($ticket);
        $ticketActivity->save();

        event(new TicketOpenEvent($ticket));
    }

    public function updated(Ticket $ticket)
    {
        if ($ticket->wasChanged('state')) {
            if ($ticket->state === 'CLOSED') {
                event(new TicketCloseEvent($ticket));
            }
            if ($ticket->state == 'ANSWERED') {
                return;
            }

            $ticketActivity = new TicketActivity([ 'type' => $ticket->state == 'OPEN' ? 'OPEN' : 'CLOSE' ]);
            $ticketActivity->ticket()->associate($ticket);
            $ticketActivity->targetable()->associate($ticket);
            $ticketActivity->save();
        }
    }

    public function deleting(Ticket $ticket)
    {
        $ticket->messages()->get()->each(fn(TicketMessage $ticketMessage) => $ticketMessage->delete());
    }

    public function creating(Ticket $ticket)
    {
        if (config('laravel-tickets.model.uuid') && empty($model->id)) {
            $ticket->id = Uuid::uuid4();
        }
    }

}
