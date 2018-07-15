@include('admin.inc.header')

<main class="py-4">
    <div class="container-fluid">
        <h1>@yield('title')</h1>
        @include('admin.inc.messages')
        @yield('content')
    </div>
</main>

@include('admin.inc.footer')