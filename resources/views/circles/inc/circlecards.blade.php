@foreach($items->chunk(3) as $circles)
    <div class="card-deck circles-overview text-center">
    @foreach($circles as $item)
        <div class="card mb-4 @if(isset($user) && $item->joined($user)) text-white bg-success @endif" data-uuid="{{ $item->uuid }}">
            <div class="card-body">
                @auth()
                <h5 class="card-title">{!! $item->link() !!}</h5>
                @else
                <h5 class="card-title">{!! $item->link((string)$item) !!}</h5>
                @endauth

                <div><span class="fa fa-language"></span>{{ list_languages($item->languages) }}</div>

                @if($item->location)
                    <div><span class="fa fa-map-marker"></span>{{ $item->location }}</div>
                @endif
            </div>

            <div class="card-footer text-center">
                <div class="mb-2">
                @for($i = 0; $i < $item->limit; $i++)
                    <div class="d-inline-block">
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