<?php


namespace LaravelTickets\Observers;

use Illuminate\Support\Facades\Storage;
use LaravelTickets\Models\TicketUpload;

class TicketUploadObserver
{

    public function deleting(TicketUpload $ticketUpload)
    {
        Storage::disk(config('laravel-tickets.file.driver'))->delete($ticketUpload->path);
    }
}
