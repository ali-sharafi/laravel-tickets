@extends(config('laravel-tickets.layouts'))

@section('content')
    <div class="card padding-25">
        <div class="card-header">
            <div class="row">
                <div class="col-1">
                    <a href="{{ route('tickets.create') }}"
                        class="btn btn-primary">{{ __('tickets.create') }}</a>
                </div>
                <div class="col-4 offset-3 text-center">
                    <h3> {{ __('tickets.title') }}</h3>
                </div>
                <div class="col-4"></div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="th">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('tickets.subject') }}</th>
                            <th scope="col">{{ __('tickets.priority') }}</th>
                            <th scope="col">{{ __('tickets.state') }}</th>
                            <th scope="col">{{ __('tickets.open_by') }}</th>
                            <th scope="col">{{ __('tickets.last_update') }}</th>
                            <th scope="col">{{ __('tickets.created_at') }}</th>
                            <th scope="col">{{ __('tickets.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <th scope="row">{{ $ticket->id }}</th>
                                <td>{{ $ticket->subject }}</td>
                                <td>{{ __('tickets.' . strtolower($ticket->priority)) }}</td>
                                <td>{{ __('tickets.' . strtolower($ticket->state)) }}</td>
                                <td>{{ $ticket->opener()->exists() ? $ticket->opener()->first()->name : $ticket->user()->first()->name }}
                                </td>
                                <td>{{ $ticket->updated_at? $ticket->updated_at: __('tickets.not_updated') }}
                                </td>
                                <td>{{ $ticket->created_at? $ticket->created_at: __('tickets.not_created') }}
                                </td>
                                <td>
                                    <a href="{{ route('tickets.show', compact('ticket')) }}"
                                        class="btn btn-primary">{{ __('tickets.show') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-2 d-flex justify-content-center">
                    {!! $tickets->links('pagination::bootstrap-4') !!}
                </div>
            </div>

        </div>
    </div>
@endsection
