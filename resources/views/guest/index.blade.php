@extends('layouts.app')

@section('title', 'Welcome to the CircleFinder!')

@section('content')

    <h1 class="text-center">@yield('title')</h1>
        
    @if(count($items) > 0)

    <p class="text-center">Join existing circles or create a new one!</p>

    <h2 class="text-center mb-4">Our newest circles</h2>

    @include('circles.inc.circlecards')
    
    @endif

@endsection