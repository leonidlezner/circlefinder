<div class="circle-filter">
    {!! Form::open(['route' => 'circles.index', 'method' => 'get', 'class' => 'form-inline justify-content-center']) !!}
        <div class="mr-2 mr-lg-0 ml-lg-2">
            {{ Form::text('location', Request::get('location', ''), ['class' => 'form-control', 'placeholder' => 'Location']) }}
        </div>
        <div class="mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('type', list_of_types(_('All types')), Request::get('type', ''), ['class' => 'selectpicker']) }}
        </div>
        <div class="mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('language', list_of_languages(_('All languages')), Request::get('language', ''), ['class' => 'selectpicker']) }}
        </div>
        <div class="mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('status', list_of_status(_('All status')), Request::get('status', ''), ['class' => 'selectpicker']) }}
        </div>
        <div class="mr-2 mr-lg-0 ml-lg-2">
        {{ Form::submit(_('Search'), ['class' => 'submit btn btn-secondary']) }}
        </div>
    {!! Form::close() !!}
</div>