@extends('layouts.app')

@section('title', 'Set my timezone')

@section('content')

<h1>@yield('title')</h1>

<div class="card mt-3">
    <div class="card-body">
    
        {!! Form::open(['route' => ['profile.timezone.update'], 'method' => 'put']) !!}

        <div class="form-group">
            {{ Form::label('timezone', 'Timezone', ['class' => 'required']) }}
            {!! Timezonelist::create('timezone', old('timezone', $item->timezone), ['class'=>'form-control']) !!}
        </div>

        {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
        <a href="{{ route('profile.index') }}" class="btn btn-light">Cancel</a>

        {!! Form::close() !!}
    </div>
</div>
@endsection