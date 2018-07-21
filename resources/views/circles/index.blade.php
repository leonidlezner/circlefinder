@extends('layouts.app')

@section('title', 'Circles')

@section('content')

    <div class="row mt-2 mb-2">
        <div class="col-12 col-lg-4">
            <h1>@yield('title')</h1>
        </div>
        <div class="col-12 col-lg-8 text-lg-right">
            <a href="{{ route('circles.create') }}" class="btn btn-success mb-4">Start a new circle!</a>
        </div>
    </div>
        
    <div class="row">
        <div class="col-12 col-lg-4">
            @include('inc.pagination')
        </div>
        <div class="col-12 col-lg-8">
            @include('circles.inc.filter')
        </div>
    </div>

    @if(count($items) > 0)
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
            <tr class="@if($item->joined($user)) font-weight-bold @endif" data-uuid="{{ $item->uuid }}">
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