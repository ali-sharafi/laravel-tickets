@extends(config('tickets.layouts'))

@section('content')
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-8">
            @includeWhen(session()->has('message'), 'laravel-tickets::alert', [
                'type' => 'info',
                'message' => session()->get('message'),
            ])
            @if ($ticket->state !== 'CLOSED')
                <div class="card mb-3">
                    <div class="card-header text-center">
                        {{ __('tickets.ticket_answer') }}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('tickets.message', compact('ticket')) }}"
                            @if (config('tickets.files')) enctype="multipart/form-data" @endif>
                            @csrf
                            <textarea class="form-control @error('message') is-invalid @enderror" placeholder="{{ __('tickets.message') }}"
                                name="message">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if (config('tickets.files'))
                                <div class="custom-file mt-2">
                                    <input type="file" name="files[]" multiple
                                        class="custom-file-input @error('files') is-invalid @enderror {{ empty($errors->get('files.*')) ? '' : 'is-invalid' }}"
                                        id="files">
                                    <label class="custom-file-label" for="files">{{ __('tickets.choose_files') }}</label>
                                    @foreach ($errors->get('files.*') as $value)
                                        <div class="invalid-feedback">{{ $value[0] }}</div>
                                    @endforeach

                                    @error('files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <button class="btn btn-primary float-right mt-2">{{ __('tickets.send') }}</button>
                        </form>
                    </div>
                </div>
            @endif
            @php($messagesPagination = $messages->paginate(4))
            @foreach ($messagesPagination as $message)
                <div class="card @if (!$loop->first) mt-2 @endif">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                {{ $message->user()->exists()? ($message->admin? $message->admin->name: $message->user->email): trans('Deleted user') }}
                            </div>
                            <div class="col-auto">
                                {{ $message->created_at }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            {!! nl2br($message->message) !!}
                        </div>
                    </div>
                    @if ($message->uploads()->count() > 0)
                        <div class="card-body border-top p-1">
                            <div class="row mt-1 mb-2 pr-2 pl-2">
                                @foreach ($message->uploads()->get() as $ticketUpload)
                                    <div class="col">
                                        <a
                                            href="{{ route('tickets.download', compact('ticket', 'ticketUpload')) }}">{{ basename($ticketUpload->path) }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach

            <div class="mt-2 d-flex justify-content-center">
                {!! $messagesPagination->links('pagination::bootstrap-4') !!}
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-4">
            <div class="card">
                <div class="card-header text-center">
                    {{ __('tickets.ticket_overview') }}
                </div>
                <div class="card-body">
                    @if (config('tickets.category') && $ticket->category()->exists())
                        <div class="form-group">
                            <label>{{ __('tickets.category') }}:</label>
                            <strong>{{ __('ticket_categories.' . $ticket->category()->first()->title) }}</strong>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>{{ __('tickets.subject') }}:</label>
                        <strong>{{ $ticket->subject }}</strong>
                    </div>
                    <div class="form-group">
                        <label>{{ __('tickets.priority') }}:</label>
                        <strong>{{ __('tickets.' . strtolower($ticket->priority)) }}</strong>
                    </div>
                    <div class="form-group">
                        <label>{{ __('tickets.state') }}:</label>
                        <strong>{{ __('tickets.' . strtolower($ticket->state)) }}</strong>
                    </div>
                    @if ($ticket->state !== 'CLOSED')
                        <form method="post" action="{{ route('tickets.close', compact('ticket')) }}">
                            @csrf
                            <button class="btn btn-block btn-danger">{{ __('tickets.close_ticket') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            <ul class="nav nav-pills mb mt-2" id="pills-tab">
                @if (config('tickets.list.users'))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-users-tab" data-toggle="pill"
                            href="#pills-users">@lang('Users')</a>
                    </li>
                @endif
                @if (config('tickets.list.files'))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-files-tab" data-toggle="pill"
                            href="#pills-files">@lang('Files')</a>
                    </li>
                @endif
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade" id="pills-users">
                    @include(
                        'laravel-tickets::partials.users',
                        compact('ticket', 'messages')
                    )
                </div>
                <div class="tab-pane fade" id="pills-files">
                    @include(
                        'laravel-tickets::partials.files',
                        compact('ticket', 'messages')
                    )
                </div>
            </div>

        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('files').addEventListener('change', fileChanged, false);

        function fileChanged(e) {
            let files = e.target.files;

            let [divFiles] = document.getElementsByClassName('custom-file');
            for (const iterator of files) {
                const span = document.createElement('span');
                span.innerHTML = iterator.name;
                span.style.color = 'green';
                span.style.display = 'block';
                span.style.padding = '5px'
                divFiles.insertAdjacentElement('beforeend', span)
            }
        }
    });
</script>
