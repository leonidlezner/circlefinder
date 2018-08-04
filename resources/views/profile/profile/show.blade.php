@extends('layouts.app')

@section('title', $item->name)

@section('content')
    
    

    <div class="card mt-3">
        <h5 class="card-header">{{ $item->name }}</h5>
        
        @if(auth()->user()->id == $item->id)
            <div class="card-body  text-center">
                <div class="btn-group">
                    <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Edit profile</a>
                    <a href="{{ route('profile.avatar.edit') }}" class="btn btn-secondary">Change avatar</a>
                    @if($item->no_password == false)
                    <a href="{{ route('profile.password.edit') }}" class="btn btn-secondary">Change password</a>
                    @endif
                </div>
            </div>
        @endif

        <div class="card-body row">
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <span class="avatar">{!! user_avatar($item) !!}</span>
                            
                            <h3 class="mt-3">{{ $item->name }}</h3>
                            
                            @if($item->moderator())
                            <span class="badge badge-success">Moderator</span>
                            @endif
                        </div>

                        @if(count($profiles))
                        <h5>Social profiles</h5>
                        <ul class="profiles">
                            @foreach($profiles as $profile => $link)
                            <li><a href="{{ $link }}"><i class="fa fa-{{ $profile }}-square"></i></a></li>
                            @endforeach
                        </ul>
                        @endif

                        @if($item->timezone)
                        <p class="mt-3">Timezone: {{ $item->timezone }}</p>
                        @else
                        <div class="mt-3 alert alert-danger">
                            <p><i class="fa fa-exclamation-triangle"></i> The timezone is not set!</p>
                            <div><a class="btn btn-primary" href="{{ route('profile.timezone.edit') }}">Set the timezone</a></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                @if($item->about)
                <div class="mb-4">
                    <h5 class="card-title">About</h5>
                    <p class="card-text">{{ $item->about }}</p>
                </div>
                @endif

                <h5 class="card-title">Owned circles</h5>

                @if(count($circles) > 0)
                    <ul>
                    @foreach($circles as $circle)
                        <li>{!! $circle->link() !!}</li>
                    @endforeach
                    </ul>
                @else
                    <p>No circles</p>
                @endif

                <h5 class="card-title">Member of circles</h5>
                
                @if(count($memberships) > 0)
                    <ul>
                    @foreach($memberships as $membership)
                        <li>{!! $membership->circle->link() !!}</li>
                    @endforeach
                    </ul>
                @else
                    <p>No circles</p>
                @endif
            </div>
        </div>
    </div>

@endsection