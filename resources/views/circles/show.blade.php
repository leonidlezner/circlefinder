@extends('layouts.app')

@section('title', $item)

@section('content')

    <h1>{{ good_title($item) }}</h1>

    @if($item->completed)
        <div class="alert alert-warning">Circle is marked as completed. No new users can join.</div>
    @else
        @if($item->full)
            <div class="alert alert-warning">Circle is full, it has reached the limit.</div>
        @endif
    @endif

    @if($item->joined($user))
        <div class="mt-4">
            <a href="{{ route('circles.membership.edit', ['uuid' => $item->uuid]) }}" class="btn btn-secondary">Edit my membership</a>

            {!! Form::open(['route' => ['circles.leave', 'uuid' => $item->uuid], 'class' => 'd-inline-block']) !!}
                {{ Form::submit('Leave circle', ['class' => 'btn btn-danger confirm']) }}
            {!! Form::close() !!}
        </div>
    @else
        @if($item->joinable($user))
            <div class="mt-4">
                {!! Form::open(['route' => ['circles.join', 'uuid' => $item->uuid], 'class' => 'd-inline-block']) !!}
                    {{ Form::submit('Join circle', ['class' => 'btn btn-success']) }}
                {!! Form::close() !!}
            </div>
        @endif
    @endif

    <div class="row">
        <div class="col-12 col-lg-7">
            @include('circles.inc.circledata')
            @include('circles.inc.members')
        </div>

        <div class="col-12 col-lg-5 mt-lg-0">
            @include('circles.inc.comments')
        </div>
    </div>

    @if($timeTable)
        @include('circles.inc.timetable')
    @endif
@endsection