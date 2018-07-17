@if(isset($item))
    {!! Form::model($item, ['route' => ['circles.update', 'uuid' => $item->uuid], 'method' => 'put']) !!}
@else
    {!! Form::open(['route' => 'circles.store']) !!}
@endif

    <div class="row mb-4">
        <div class="col-12 col-lg-6">
            <div class="form-group">
                {{ Form::label('type', 'Type', ['class' => 'required']) }}
                {{ Form::select('type', list_of_types(), null, ['class' => 'form-control']) }}
            </div>

            <div class="row">
                <div class="form-group col-6">
                    {{ Form::label('begin', 'Begin', ['class' => 'required']) }}
                    {{ Form::date('begin', isset($item) ? $item->begin->format('Y-m-d') : today(), ['class' => 'form-control']) }}
                </div>
                <div class="form-group col-6">
                    {{ Form::label('limit', 'Limit of members') }}
                    {{ Form::number('limit', isset($item) ? null : config('circle.defaults.limit'), ['class' => 'form-control', 'min' => config('circle.limit.min'), 'max' => config('circle.limit.max')]) }}
                </div>
            </div>

            @include('inc.form-languages')

            @include('inc.form-location')
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                {{ Form::label('title', 'Title') }}
                {{ Form::text('title', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('description', 'Description') }}
                {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>

    @if(isset($item))
        {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
    @else
        {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
    @endif
    
    @if(isset($item))
    {!! $item->link('Cancel', 'btn btn-light') !!}
    @else
    <a href="{{ route('circles.index') }}" class="btn btn-light">Cancel</a>
    @endif
{!! Form::close() !!}