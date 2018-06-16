@extends('layouts.app')

@section('title', $item->name)

@section('content')
    
    

    <div class="card mt-3">
        <h5 class="card-header">{{ $item->name }}</h5>
        
        @if(auth()->user()->id == $item->id)
            <div class="card-body">    
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit profile</a>
                <a href="{{ route('profile.avatar.edit') }}" class="btn btn-primary">Change avatar</a>
                <a href="{{ route('profile.password.edit') }}" class="btn btn-primary">Change password</a>
            </div>
        @endif

        <div class="card-body row">

            <div class="col-8">
                <h5 class="card-title">About</h5>
                <p class="card-text">{{ $item->about }}</p>
            </div>

            <div class="col-4">
                <div class="mb-4">
                    <img src="{{ route('profile.avatar.download', ['file' => $item->avatar]) }}" alt="{{ $item->name }}" />
                </div>

                <h5 class="card-title">Social profiles</h5>
                @if($item->facebook_profile_url)
                <li><a href="{{ $item->facebook_profile_url }}">Facebook</a></li>
                @endif

                @if($item->twitter_profile_url)
                <li><a href="{{ $item->twitter_profile_url }}">Twitter</a></li>
                @endif

                @if($item->yammer_profile_url)
                <li><a href="{{ $item->yammer_profile_url }}">Yammer</a></li>
                @endif

                @if($item->linkedin_profile_url)
                <li><a href="{{ $item->linkedin_profile_url }}">LinkedIn</a></li>
                @endif
            </div>
        </div>
    </div>

@endsection