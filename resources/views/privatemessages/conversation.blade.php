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
                <div class="col-12 col-lg-12">
                    <h2>Conversation started by {{ $conversation->sender->name }}</h2>
                    <div class="list-group">
                        @foreach($privateMessages as $privateMessage)
                            <div class="list-group-item flex-column align-items-start @if(null === $privateMessage->read_at) list-group-item-secondary @endif">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">From <strong>{{ $privateMessage->sender->name }}</strong> to <strong>{{ $privateMessage->recipient->name }}</strong></h5>
                                    <small>{{ $privateMessage->created_at }}</small>
                                </div>
                                <p class="mb-1">{{ $privateMessage->body }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- 
            @if($auth_id !== $conversation->user_id)
            <a href="{{ route('private_messages.reply', ['uuid' => $privateMessage->sender->uuid, 'replyUuid' => $privateMessage->uuid]) }}" class="btn btn-success">Reply</a>
            @endif
            <a href="{{ route('private_messages.inbox') }}" class="btn btn-light">Cancel</a>
             --}}
        </div>
    </div>

@endsection
