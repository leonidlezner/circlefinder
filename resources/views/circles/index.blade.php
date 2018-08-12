@extends('layouts.app')

@section('title', 'Circles')

@section('content')

    <div class="row mt-2 mb-2">
        <div class="col-12 col-lg-4">
            <h1>@yield('title')</h1>
        </div>
        <div class="col-12 col-lg-8 text-lg-right">
            <a href="{{ route('circles.create') }}" class="btn btn-success mb-4">Start a new circle!</a>
        </div>
    </div>
        
    <div class="row">
        <div class="col-12 col-lg-4">
            @include('inc.pagination')
        </div>
        <div class="col-12 col-lg-8">
            @include('circles.inc.filter')
        </div>
    </div>

    @if(count($items) > 0)
        @foreach($items->chunk(3) as $circles)
            <div class="card-deck circles-overview">
            @foreach($circles as $item)
                <div class="card mb-4 @if($item->joined($user)) text-white bg-success @endif" data-uuid="{{ $item->uuid }}">
                    <div class="card-body">
                        <h5 class="card-title">{!! $item->link() !!}</h5>
                        
                        @if($item->location)
                            <div><span class="fa fa-map-marker"></span>{{ $item->location }}</div>
                        @endif

                        <div><span class="fa fa-language"></span>{{ list_languages($item->languages) }}</div>
                    </div>

                    <div class="card-footer text-center">
                        <div class="row mb-2">
                        @for($i = 0; $i < $item->limit; $i++)
                            <div class="col">
                                @if($i < count($item->memberships))
                                <span class="fa fa-user"></span>
                                @else
                                <span class="fa fa-circle-o"></span>
                                @endif
                            </div>
                        @endfor
                        </div>

                        <div class="row">
                            <div class="col">
                                <small class="d-block">{{ _('Begin') }}</small>
                                {{ format_date($item->begin) }}
                            </div>
                            <div class="col">
                                <small class="d-block">{{ _('Status') }}</small>
                                {{ circle_state($item) }}
                            </div>
                            <div class="col">
                                <small class="d-block">{{ _('Presence') }}</small>
                                {{ translate_type($item->type) }}
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
            </div>
        @endforeach

        {{-- 
        <table class="table table-striped table-bordered">
            <tr>
                <th>Circle</th>
                <th>Begin</th>
                <th>Status</th>
                <th>Type (virtual/f2f)</th>
                <th>Language</th>
            </tr>
            
            @foreach($items as $item)
            <tr class="@if($item->joined($user)) font-weight-bold @endif" data-uuid="{{ $item->uuid }}">
                <td class="align-middle">{!! $item->link($item->id) !!}</td>
                <td class="align-middle">{!! $item->link($item->title) !!} <div><small>{{ $item->location }}</small></div></td>
                <td class="align-middle"></td>
                <td class="align-middle"></td>
                <td class="align-middle"></td>
                <td class="align-middle">{{ list_languages($item->languages, 3)}}</td>
            </tr>
            @endforeach
        </table>
        --}}

        @include('inc.pagination')

    @else
        <p>No circles were found</p>
    @endif

@endsection