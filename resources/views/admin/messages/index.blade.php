@extends('admin.layouts.app')

@section('title', 'Messages')

@section('content')

    @if(count($items) > 0)

    @include('admin.inc.pagination')

    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <th>Show to all</th>
            <th>Body</th>
            <th>Author</th>
            <th>Circle</th>
        </tr>
        
        @foreach($items as $item)
        <tr class="item-{{ $item->id }}">
            <td class="align-middle"><a href="{{ route('admin.messages.show', ['id' => $item->id]) }}">{{ $item->id }}</a></td>
            <td class="align-middle">{{ $item->show_to_all ? 'Yes' : 'No' }}</td>
            <td class="align-middle w-50">{{ $item->body }}</td>
            <td class="align-middle">{{ $item->user->name }}</td>
            <td class="align-middle">{!! $item->circle->link() !!}</td>
        </tr>
        @endforeach
    </table>
    
    @include('admin.inc.pagination')

    @else
        <p>No circles were found</p>
    @endif
@endsection