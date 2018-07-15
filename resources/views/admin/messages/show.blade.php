@extends('admin.layouts.app')

@section('title', $item)

@section('content')
    
    <p>Title: {{ $item->title }}</p>
    <p>Completed: {{ $item->completed ? 'Yes': 'No' }}</p>
    <p>Limit: {{ $item->limit }}</p>
    <p>Type: {{ $item->type }}</p>
    <p>Members: {{ count($item->memberships) }}</p>

    @if(count($item->memberships))
        <h2>Memberships</h2>

        <ul>
        @foreach($item->memberships as $membership)
            <li>{{ $membership->user->name }}</li>
        @endforeach
        </ul>
    @endif

@endsection