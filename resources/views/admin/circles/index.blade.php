@extends('admin.layouts.app')

@section('title', 'Circles')

@section('content')

    @if(count($items) > 0)

    @include('admin.inc.pagination')

    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Begin</th>
            <th>Completed</th>
            <th>Members/Limit</th>
            <th>Type (virtual/f2f)</th>
            <th>Messages</th>
            <th>Owner</th>
        </tr>
        
        @foreach($items as $item)
        <tr class="item-{{ $item->id }}">
            <td class="align-middle">{{ $item->id }}</td>
            <td class="align-middle"><a href="{{ route('admin.circles.show', ['id' => $item->id]) }}">{{ good_title($item) }}</a></td>
            <td class="align-middle">{{ format_date($item->begin) }}</td>
            <td class="align-middle">{{ $item->completed ? 'Yes': 'No' }}</td>
            <td class="align-middle">{{ $item->memberships()->count() }} / {{ $item->limit }}</td>
            <td class="align-middle">{{ translate_type($item->type) }}</td>
            <td class="align-middle">{{ count($item->messages) }}</td>
            <td class="align-middle">{{ $item->user->name }}</td>
        </tr>
        @endforeach
    </table>
    
    @include('admin.inc.pagination')

    @else
        <p>No circles were found</p>
    @endif
@endsection