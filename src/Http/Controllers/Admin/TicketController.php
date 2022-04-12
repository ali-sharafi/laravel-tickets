<?php

namespace LaravelTickets\Http\Controllers\Admin;

use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Http\Controllers\BaseController;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketComment;
use LaravelTickets\Models\TicketMessage;

class TicketController extends BaseController
{
    public function index()
    {
        $state = $this->request->state ?? null;
        $category = $this->request->category ?? null;
        $label = $this->request->label ?? null;
        $pageSize = $this->request->page_size ?? 20;

        return $this->responseSuccess(Ticket::with(['category', 'user', 'agent', 'label'])
            ->label($label)
            ->category($category)
            ->state($state)
            ->paginate($pageSize));
    }

    public function show(Ticket $ticket)
    {
        $messages = $ticket->messages()->with(['user', 'uploads'])->orderBy('created_at', 'desc')->get();
        $comments = $ticket->comments()->with('user')->orderBy('created_at', 'desc')->get();

        return $this->responseSuccess([
            'messages' => $messages,
            'comments' => $comments,
            'ticket' => $ticket
        ]);
    }

    /**
     * Send a message to the @param Request $request
     *
     * @param Ticket $ticket
     *
     * @return RedirectResponse|void
     * @link Ticket
     *
     */
    public function message(Ticket $ticket)
    {
        $data  = $this->validateMessageRequest();
        $ticketComment = null;
        $ticketMessage = null;

        if (isset($data['message'])) {
            $ticketMessage = $this->insertMessage($data, $ticket);
            $this->handleFiles($data['files'] ?? [], $ticketMessage);
        }

        if (isset($data['comment'])) $ticketComment = $this->insertComment($data['comment'], $ticket);


        $ticket->update(['state' => $this->request->close_ticket ? 'CLOSED' : 'OPEN']);

        return $this->responseSuccess([
            'message' => $ticketMessage ? $ticketMessage->load('uploads') : $ticketMessage,
            'comment' => $ticketComment
        ]);
    }

    public function deleteComment(TicketComment $comment)
    {
        if ($comment->user_id !== $this->request->user()->id) abort(403, "You're not owner of this comment.");
        $comment->delete();
        return $this->responseSuccess();
    }

    private function insertComment($comment, Ticket $ticket)
    {
        $ticketComment = new TicketComment(['message' => $comment]);
        $ticketComment->user()->associate($this->request->user());
        $ticketComment->ticket()->associate($ticket);
        $ticketComment->save();

        return $ticketComment;
    }

    private function insertMessage($data, Ticket $ticket)
    {
        $data['user_type'] = TicketInterface::ADMIN;
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($this->request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        return $ticketMessage;
    }

    public function reassign(Ticket $ticket)
    {
        $ticket->update(['category_id' => $this->request->category_id, 'label_id' => $this->request->label_id ?? null]);

        return $this->responseSuccess();
    }

    /**
     * Declare the @param Ticket $ticket
     *
     * @return RedirectResponse|void
     * @link Ticket as closed.
     *
     */
    public function open(Ticket $ticket)
    {
        if ($ticket->state === 'OPEN') {
            return $this->responseError('Ticket already OPENED.');
        }

        $ticket->update(['state' => 'OPEN']);

        return $this->responseSuccess();
    }

    /**
     * Declare the @param Ticket $ticket
     *
     * @return RedirectResponse|void
     * @link Ticket as closed.
     *
     */
    public function close(Ticket $ticket)
    {
        if ($ticket->state === 'CLOSED') {
            return $this->responseError('Ticket already closed.');
        }

        $ticket->update(['state' => 'CLOSED']);

        return $this->responseSuccess();
    }


    /**
     * Handles the uploaded files for the @param $files array uploaded files
     *
     * @param TicketMessage $ticketMessage
     *
     * @link TicketMessage
     *
     */
    private function handleFiles($files, TicketMessage $ticketMessage)
    {
        if (!config('laravel-tickets.files') || $files == null) {
            return;
        }
        foreach ($files as $file) {
            $ticketMessage->uploads()->create([
                'path' => $file->storeAs(
                    config('laravel-tickets.file.path') . $ticketMessage->id,
                    $file->getClientOriginalName(),
                    config('laravel-tickets.file.driver')
                )
            ]);
        }
    }

    private function validateMessageRequest()
    {
        return $this->request->validate([
            'message' => 'string',
            'comment' => 'string',
            'files' => ['max:' . config('laravel-tickets.file.max-files')],
            'files.*' => [
                'sometimes',
                'file',
                'max:' . config('laravel-tickets.file.size-limit')
            ]
        ]);
    }
}
