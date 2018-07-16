@extends('layouts.app')

@section('title', 'Welcome to the CircleFinder!')

@section('content')

    <h1>@yield('title')</h1>
        
    @if(count($items) > 0)

    <p>Join existing circles or create a new one!</p>

    <h2>Our newest circles</h2>

    <table class="table table-striped table-bordered">
        <tr>
            <th>Name</th>
            <th>Begin</th>
            <th>Status</th>
            <th>Type (virtual/f2f)</th>
        </tr>
        
        @foreach($items as $item)
        <tr>
            <td class="align-middle">
                {!! $item->link('Circle '.$item->id) !!}
                <small class="d-block">{{ $item->location }}</small>
            </td>
            <td class="align-middle">{{ format_date($item->begin) }}</td>
            <td class="align-middle">{{ circle_state($item) }}</td>
            <td class="align-middle">{{ translate_type($item->type) }}</td>
        </tr>
        @endforeach
    </table>
    
    @endif

@endsection