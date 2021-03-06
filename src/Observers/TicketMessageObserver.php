<?php


namespace LaravelTickets\Observers;

use Illuminate\Support\Facades\Storage;
use LaravelTickets\Events\TicketMessageEvent;
use LaravelTickets\Models\TicketActivity;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Models\TicketUpload;

class TicketMessageObserver
{

    public function created(TicketMessage $ticketMessage)
    {
        $ticketActivity = new TicketActivity(['type' => 'ANSWER']);
        $ticketActivity->ticket()->associate($ticketMessage->ticket()->first());
        $ticketActivity->targetable()->associate($ticketMessage);
        $ticketActivity->save();

        $ticket = $ticketMessage->ticket()->first();

        if ($ticketMessage->user_id != $ticket->user_id) {
            $ticket->update(['state' => 'ANSWERED']);
        }

        event(new TicketMessageEvent($ticket, $ticketMessage));
    }

    public function deleting(TicketMessage $ticketMessage)
    {
        $ticketMessage->uploads()->get()->each(fn (TicketUpload $ticketUpload) => $ticketUpload->delete());
        Storage::disk(config('tickets.file.driver'))
            ->deleteDirectory(config('tickets.file.path') . $ticketMessage->id);
    }
}
