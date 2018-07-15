<div class="card mt-4">
    <h5 class="card-header">Circle data</h5>

    <div class="card-body">
        @can('update', $item)
            <div class="mb-4">
                <a href="{{ route('circles.edit', ['uuid' => $item->uuid]) }}" class="btn btn-secondary">Edit circle</a>
    
                {!! Form::open(['route' => ['circles.'.($item->completed ? 'uncomplete' : 'complete'), 'uuid' => $item->uuid], 'class' => 'd-inline-block']) !!}
                    {{ Form::submit($item->completed ? 'Uncomplete' : 'Complete', ['class' => 'btn btn-primary confirm']) }}
                {!! Form::close() !!}
    
                @if($item->deletable())
                {!! Form::open(['route' => ['circles.destroy', 'uuid' => $item->uuid], 'class' => 'd-inline-block']) !!}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete circle', ['class' => 'btn btn-danger confirm']) }}
                {!! Form::close() !!}
                @endif
            </div>
        @endcan

        <ul class="list-group circle-data">
            <li class="list-group-item"><span class="fa fa-users"></span>Presence: <strong>{{ translate_type($item->type) }}</strong></li>
            <li class="list-group-item"><span class="fa fa-calendar"></span>Begin: <strong>{{ format_date($item->begin) }}</strong></li>
            @if($item->location)
                <li class="list-group-item"><span class="fa fa-map-marker"></span>Location: <strong>{{ $item->location }}</strong></li>
            @endif
            <li class="list-group-item"><span class="fa fa-language"></span>Languages: <strong>{{ list_languages($item->languages) }}</strong></li>
            @if($item->description)
                <li class="list-group-item"><span class="fa fa-align-left"></span>Description: {{ $item->description }}</li>
            @endif
            <li class="list-group-item"><span class="fa fa-user"></span>Owner: <strong>{!! $item->user->link() !!}</strong></li>
        </ul>

    </div>
</div>