@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<p>Users: {{ \App\User::count() }}</p>
<p>Circles: {{ \App\Circle::count() }}</p>
<p>Memberships: {{ \App\Membership::count() }}</p>
<p>Comments: {{ \App\Message::count() }}</p>

@endsection