@extends('layouts.app')

@section('title', 'Change my profile')

@section('content')

<h1>@yield('title')</h1>

<div class="card mt-3">
        <h5 class="card-header">{{ $item->name }}</h5>
        <div class="card-body">
        
            {!! Form::model($item, ['route' => ['profile.update'], 'method' => 'put']) !!}


            <div class="form-group">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('email', 'E-Mail') }}
                {{ Form::email('email', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('about', 'About') }}
                {{ Form::textarea('about', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('twitter_profile_url', 'Twitter') }}
                {{ Form::text('twitter_profile_url', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('facebook_profile_url', 'Facebook') }}
                {{ Form::text('facebook_profile_url', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('yammer_profile_url', 'Yammer') }}
                {{ Form::text('yammer_profile_url', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('linkedin_profile_url', 'LinkedIn') }}
                {{ Form::text('linkedin_profile_url', null, ['class' => 'form-control']) }}
            </div>

            {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
            <a href="{{ route('profile.index') }}" class="btn btn-light">Cancel</a>

            {!! Form::close() !!}
        </div>
</div>
@endsection