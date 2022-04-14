<?php

namespace LaravelTickets\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketCategory;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Models\TicketUpload;

class TicketController
{
    protected $request;

    public function __construct(Request $request)
    {
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

        return $this->sendResponse(compact('tickets'), Response::HTTP_OK, 'laravel-tickets::index');
    }

    /**
     * Show the create form
     *
     * @return View
     */
    public function create()
    {
        $categories = TicketCategory::all();

        return $this->sendResponse(compact('categories'), Response::HTTP_OK, 'laravel-tickets::create');
    }

    /**
     * Create a new ticket
     *
     * @return View|RedirectResponse|Response
     * @link Ticket
     *
     */
    public function store()
    {
        $data = $this->validateTicketRequest();

        if ($this->isTicketCountReachMax())
            return $this->request->wantsJson() ?
                $this->sendResponse(['message' => trans('tickets.reach_max_open_tickets')], Response::HTTP_UNPROCESSABLE_ENTITY) :
                back()->with('message', trans('tickets.reach_max_open_tickets'));

        $ticket = $this->request->user()->tickets()->create($data);

        $data['user_type'] = TicketInterface::USER;
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($this->request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        if ($this->request->wantsJson()) return $this->index();

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
        if (!$ticket->user()->get()->contains($this->request->user()))
            return abort(403);

        $messages = $ticket->messages()->with(['user', 'uploads'])->orderBy('created_at', 'desc');

        return $this->sendResponse(compact(
            'ticket',
            'messages'
        ), Response::HTTP_OK, 'laravel-tickets::show');
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
        if (!$ticket->user()->get()->contains($this->request->user())) {
            return abort(403);
        }

        $data  = $this->validateMessageRequest();

        if (!config('laravel-tickets.open-ticket-with-answer') && $ticket->state === TicketInterface::STATE_CLOSED) {
            if ($this->request->wantsJson())
                return $this->sendResponse(['message' => trans('tickets.can_not_reply_to_closed_ticket')]);
            return back()->with('message', trans('tickets.can_not_reply_to_closed_ticket'));
        }

        $data['user_type'] = TicketInterface::USER;
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($this->request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        $ticket->update(['state' => 'OPEN']);

        if (!$this->request->wantsJson())
            return  back()->with(
                'message',
                trans('tickets.message_sent_successfully')
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
            if ($this->request->wantsJson())
                return $this->sendResponse(['message' => $message], Response::HTTP_UNPROCESSABLE_ENTITY);
            return back()->with(
                'message',
                $message
            );
        }

        $ticket->update(['state' => 'CLOSED']);

        if (!$this->request->wantsJson())
            return back()->with(
                'message',
                trans('tickets.ticket_closed_successfully')
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
        if (!$ticket->user()->get()->contains($this->request->user())) {
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
            'message' => [
                'required', 'string',
                Rule::unique(config('laravel-tickets.ticket-messages-table'))
                    ->where('user_id', $this->request->user()->id)
            ],
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
            'subject' => ['required', 'string', 'max:191'],
            'priority' => ['required', Rule::in(config('laravel-tickets.priorities'))],
            'message' => ['required', 'string'],
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
                Rule::exists(config('laravel-tickets.ticket-categories-table'), 'id'),
            ];
        }

        return $this->request->validate($rules);
    }

    /**
     * Return response depends on request type
     * 
     * @var $data
     * @var $page
     * 
     * @return Json|View
     */
    private function sendResponse($data, $status = 200, $page = null)
    {
        if ($this->request->wantsJson())
            return response(['data' => $data], $status);
        return view($page, $data);
    }
}
