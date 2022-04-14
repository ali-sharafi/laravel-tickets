@extends(config('tickets.layouts'))

@section('content')
    <div class="card padding-25">
        <div class="card-header text-center">
            <h3>{{ __('tickets.open_ticket') }}</h3>
        </div>
        <div class="card-body">
            @includeWhen(session()->has('message'), 'laravel-tickets::alert', [
                'type' => 'info',
                'message' => session()->get('message'),
            ])
            <form method="post" action="{{ route('tickets.store') }}"
                @if (config('tickets.files')) enctype="multipart/form-data" @endif>
                @csrf
                <div class="row">
                    @if (config('tickets.category'))
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('tickets.category') }}</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                    @foreach ($categories as $ticketCategory)
                                        <option value="{{ $ticketCategory->id }}"
                                            @if (old('category_id') === $ticketCategory->id) selected @endif>
                                            {{ __('ticket_categories.' . $ticketCategory->title) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="col-4">
                        <div class="form-group">
                            <label>{{ __('tickets.priority') }}</label>
                            <select class="form-control @error('priority') is-invalid @enderror" name="priority">
                                @foreach (config('tickets.priorities') as $priority)
                                    <option value="{{ $priority }}" @if (old('priority') === $priority) selected @endif>
                                        {{ __('tickets.' . strtolower($priority)) }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <label>{{ __('tickets.subject') }}</label>
                            <input class="form-control @error('subject') is-invalid @enderror" name="subject"
                                placeholder="{{ __('tickets.subject') }}" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('tickets.message') }}</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" placeholder="{{ __('tickets.message') }}"
                                name="message">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @if (config('tickets.files'))
                        <div class="col-12 mb-2">
                            <div class="custom-file">
                                <input type="file" name="files[]" multiple
                                    class="custom-file-input @error('files') is-invalid @enderror" id="files">
                                <label class="custom-file-label" for="files">{{ __('tickets.choose_files') }}</label>
                                @error('files')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <button class="btn btn-primary">{{ __('tickets.create') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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
    </script>
@endsection
