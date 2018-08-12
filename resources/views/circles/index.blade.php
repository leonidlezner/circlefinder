@extends('layouts.app')

@section('title', 'Circles')

@section('content')

    <div class="row mt-2 mb-2 text-center text-lg-left">
        <div class="col-12 col-lg-4">
            <h1>@yield('title')</h1>
        </div>
        <div class="col-12 col-lg-8 text-lg-right">
            <a href="{{ route('circles.create') }}" class="btn btn-success mb-4">Start a new circle!</a>
        </div>
    </div>
    
    <div class="card mb-4 bg-light">
        <div class="card-body">
            @include('circles.inc.filter')
        </div>
    </div>

    @if(count($items) > 0)
        @include('inc.pagination', ['justify' => 'justify-content-center'])
        @include('circles.inc.circlecards')
        @include('inc.pagination', ['justify' => 'justify-content-center'])
    @else
        <p>No circles were found</p>
    @endif

@endsection