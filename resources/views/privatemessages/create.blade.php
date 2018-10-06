@extends('layouts.app')

@section('title', 'New Private Message')

@section('content')

<h1>@yield('title')</h1>

<div class="card mt-3">
        <h5 class="card-header">Send a private message to {{ $recipient->name }}</h5>
        <div class="card-body">
            @include('privatemessages.form')
        </div>
</div>

@endsection
