@extends('layouts.app')

@section('title', 'Change my avatar')

@section('content')

<h1>@yield('title')</h1>

<div class="card mt-3">
        
        <div class="card-body">

            <h2>Current avatar</h2>

            <div class="mb-4 mt-4">
                <img src="{{ route('profile.avatar.download', ['file' => $user->avatar]) }}" alt="{{ $user->name }}" />
            </div>

            <h2>Upload new avatar</h2>
        
            {!! Form::open(['route' => ['profile.avatar.update'], 'method' => 'put', 'files' => true]) !!}

            <div class="custom-file mt-3 mb-4">
                {{ Form::file('avatar', ['class' => 'custom-file-input']) }}
                {{ Form::label('avatar', 'Choose file', ['class' => 'custom-file-label']) }}
            </div>
            
            {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
            <a href="{{ route('profile.index') }}" class="btn btn-light">Cancel</a>

            {!! Form::close() !!}
        </div>
</div>
@endsection