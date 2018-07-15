<div class="card mt-4 comments" data-circle="{{ $item->uuid }}">
    <h5 class="card-header">Comments</h5>
    
    <div class="card-body">
        @if($membership || $user->moderator())
            @if(count($messages))
                <div class="mb-3">
                @foreach($messages as $message)
                    <div class="media message {{ $message->show_to_all ? "show-to-all" : "" }}" data-uuid="{{ $message->uuid }}">
                        <div class="mr-2">
                            <span class="avatar">{!! user_avatar($message->user, 30, false, true) !!}</span>
                        </div>
                        <div class="media-body mb-2">
                            <strong class="mt-0">{!! $message->user->link() !!} @can('update', $message) · <a href="#" class="edit text-success">edit</a>@endcan</strong>
                            
                            <div class="body">{{ $message->body }}</div>

                            <small class="show-to-all-info text-secondary mt-1"><span class="fa fa-eye"></span> Visible to all members of this circle</small>
                        </div>
                    </div>
                @endforeach
                </div>
            @else
            <p>No visible comments yet</p>
            @endif
        @endif

        @can('create', [\App\Message::class, $item])
            <div>
                {!! Form::open(['route' => ['circles.messages.store', 'uuid' => $item->uuid]]) !!}
                    <div class="form-group">
                        {{ Form::label('body', 'Comment') }}
                        {{ Form::textarea('body', null, ['class' => 'form-control', 'required' => true, 'rows' => 2]) }}
                    </div>

                    <div class="text-secondary mb-1"><span class="fa fa-eye"></span> New members will see the comment only if "Show to all members" is checked.</div>

                    <div class="form-group">
                        {{ Form::checkbox('show_to_all', true, null, ['id' => 'show_to_all']) }}
                        {{ Form::label('show_to_all', 'Show to all members') }}
                    </div>

                    {{ Form::submit('Post comment', ['class' => 'btn btn-success']) }}
                {!! Form::close() !!}
            </div>
        @else
            <div><span class="fa fa-lock"></span> Only circle members can read and post comments!</div>
        @endcan
    </div>
</div>

