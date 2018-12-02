@extends('layouts.app')

@section('title', 'Private Messages')

@section('content')

    <div class="row mt-2 mb-2 text-center text-lg-left">
        <div class="col-12 col-lg-4">
            <h1>@yield('title')</h1>
        </div>
    </div>
    
    <div class="card mb-4 bg-light">
        <h5 class="card-header">Your private messages</h5>
        <div class="card-body">
            <div class="card-body row">
                
                <div class="col-12">
                    @if(count($items) > 0)
                        <h2>Conversations</h2>
                        <div class="list-group">
                        @foreach($items as $privateMessage)
                            <a href="{{ route('private_messages.read', ['uuid' => $privateMessage->uuid]) }}" id="{{ $privateMessage->uuid }}" class="list-group-item list-group-item-action flex-column align-items-start @if(null === $privateMessage->read_at) list-group-item-secondary @endif">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">From <strong>{{ $privateMessage->sender->name }}</strong> to <strong>{{ $privateMessage->recipient->name }}</strong></h5>
                                    <small>{{ $privateMessage->created_at }}</small>
                                </div>
                                <p class="mb-1">{{ str_limit($privateMessage->body, $limit = 150, $end = '...') }}</p>
                            </a>
                        @endforeach
                        </div>
                    @else
                        <p>No messages were found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
