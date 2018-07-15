@extends('layouts.app')

@section('title', 'Circles')

@section('content')

    <h1>@yield('title')</h1>

    <a href="{{ route('circles.create') }}" class="btn btn-success mb-4">New circle</a>
        
    @if(count($items) > 0)

        @include('inc.pagination')

        <table class="table table-striped table-bordered">
            <tr>
                <th>Circle</th>
                <th>Title</th>
                <th>Begin</th>
                <th>Status</th>
                <th>Type (virtual/f2f)</th>
                <th>Language</th>
            </tr>
            
            @foreach($items as $item)
            <tr class="item-{{ $item->id }} @if($item->joined($user)) font-weight-bold @endif">
                <td class="align-middle">{!! $item->link($item->id) !!}</td>
                <td class="align-middle">{!! $item->link($item->title) !!} <div><small>{{ $item->location }}</small></div></td>
                <td class="align-middle">{{ format_date($item->begin) }}</td>
                <td class="align-middle">{{ circle_state($item) }}</td>
                <td class="align-middle">{{ translate_type($item->type) }}</td>
                <td class="align-middle">{{ list_languages($item->languages, 3)}}</td>
            </tr>
            @endforeach
        </table>

        @include('inc.pagination')

    @else
        <p>No circles were found</p>
    @endif

@endsection