{!! Form::open(['route' => ['private_messages.send', 'uuid' => $recipient->uuid]]) !!}

    <div class="row mb-4">
        <div class="col-12 col-lg-12">
            <div class="form-group">
                {{ Form::label('body', 'Message') }}
                {{ Form::textarea('body', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    @if(null !== $replyToMessage)
    <div class="row mb-4">
        <div class="col-12 col-lg-12">
            <div class="list-group">
                <div id="{{ $replyToMessage->uuid }}" class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">{{ $replyToMessage->user->name }}</h5>
                        <small>{{ $replyToMessage->created_at }}</small>
                    </div>
                    <p class="mb-1">{{ $replyToMessage->body }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{ Form::submit('Send', ['class' => 'btn btn-success']) }}
     <a href="{{ route('private_messages.inbox') }}" class="btn btn-light">Cancel</a>

{!! Form::close() !!}
