<?php

namespace LaravelTickets\Http\Controllers;

use App\Http\Controllers\Page\PageController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketCategory;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Models\TicketUpload;

class TicketController extends PageController
{
    protected $request;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    /**
     * Show every @return View
     *
     * @link Ticket that the user has created
     *
     *
     */
    public function index()
    {
        $tickets = $this->request->user()->tickets();
        $tickets = $tickets->with('user')->orderBy('id', 'desc')->paginate(10);

        return $this->showPage('laravel-tickets::index', ['tickets' => $tickets]);
    }

    /**
     * Show the create form
     *
     * @return View
     */
    public function create()
    {
        $categories = TicketCategory::all();
        return $this->showPage('laravel-tickets::create', ['categories' => $categories]);
    }

    /**
     * Create a new ticket
     *
     * @return View|RedirectResponse
     * @link Ticket
     *
     */
    public function store()
    {
        $data = $this->validateTicketRequest();

        if ($this->isTicketCountReachMax()) return back()->with('message', trans('tickets.reach_max_open_tickets'));

        $ticket = $this->request->user()->tickets()->create($data);

        $data['user_type'] = TicketInterface::USER;
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($this->request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        return redirect(route('tickets.show', compact('ticket')))->with('message', trans('tickets.ticket_created_successfully'));
    }

    /**
     * Show detailed informations about the @param Ticket $ticket
     *
     * @return View|RedirectResponse|void
     * @link Ticket and the informations
     *
     */
    public function show(Ticket $ticket)
    {
        if (!$ticket->user()->get()->contains(\request()->user()))
            return abort(403);

        $messages = $ticket->messages()->with(['user', 'uploads'])->orderBy('created_at', 'desc');

        return $this->showPage('laravel-tickets::show', compact(
            'ticket',
            'messages'
        ));
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
        if (!$ticket->user()->get()->contains(\request()->user())) {
            return abort(403);
        }

        $data  = $this->validateMessageRequest();

        if (!config('laravel-tickets.open-ticket-with-answer') && $ticket->state === 'CLOSED') {
            return back()->with('message', trans('tickets.can_not_reply_to_closed_ticket'));
        }

        $data['user_type'] = TicketInterface::USER;
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($this->request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        $ticket->update(['state' => 'OPEN']);

        $message = trans('tickets.message_sent_successfully');
        return  back()->with(
            'message',
            $message
        );
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
        if (!$ticket->user()->get()->contains(\request()->user())) {
            return abort(403);
        }

        if ($ticket->state === 'CLOSED') {
            $message = trans('tickets.ticket_already_closed');
            return back()->with(
                'message',
                $message
            );
        }

        $ticket->update(['state' => 'CLOSED']);

        $message = trans('tickets.ticket_closed_successfully');
        return back()->with(
            'message',
            $message
        );
    }

    /**
     * Downloads the file from @param Ticket $ticket
     *
     * @param TicketUpload $ticketUpload
     *
     * @return BinaryFileResponse
     * @link TicketUpload
     *
     */
    public function download(Ticket $ticket, TicketUpload $ticketUpload)
    {
        if (!$ticket->user()->get()->contains(\request()->user())) {
            return abort(403);
        }

        $storagePath = storage_path('app/' . $ticketUpload->path);
        if (config('laravel-tickets.pdf-force-preview') && pathinfo($ticketUpload->path, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file($storagePath);
        }

        return response()->download($storagePath);
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

    private function isTicketCountReachMax()
    {
        return $this->request->user()->tickets()->where('state', '!=', 'CLOSED')->count() >= config('laravel-tickets.maximal-open-tickets');
    }

    private function validateMessageRequest()
    {
        return $this->request->validate([
            'message' => ['required', 'string', 'profanity', Rule::unique(config('laravel-tickets.database.ticket-messages-table'))->where('user_id', $this->request->user()->id)],
            'files' => ['max:' . config('laravel-tickets.file.max-files')],
            'files.*' => [
                'sometimes',
                'file',
                'max:' . config('laravel-tickets.file.size-limit'),
                'mimes:' . config('laravel-tickets.file.mimetype'),
            ]
        ]);
    }

    private function validateTicketRequest()
    {
        $rules = [
            'subject' => ['required', 'string', 'max:191', 'profanity'],
            'priority' => ['required', Rule::in(config('laravel-tickets.priorities'))],
            'message' => ['required', 'string', 'profanity'],
            'files' => ['max:' . config('laravel-tickets.file.max-files')],
            'files.*' => [
                'sometimes',
                'file',
                'max:' . config('laravel-tickets.file.size-limit'),
                'mimes:' . config('laravel-tickets.file.mimetype'),
            ],
        ];

        if (config('laravel-tickets.category')) {
            $rules['category_id'] = [
                'required',
                Rule::exists(config('laravel-tickets.database.ticket-categories-table'), 'id'),
            ];
        }

        return $this->request->validate($rules);
    }
}
