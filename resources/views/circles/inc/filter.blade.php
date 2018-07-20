<div class="mb-2 circle-filter">
    {!! Form::open(['route' => 'circles.index', 'method' => 'get', 'class' => 'form-inline justify-content-lg-end']) !!}
        <div class="mb-2 mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('type', list_of_types(), Request::get('type', ''), ['title' => _('Type'), 'class' => 'selectpicker']) }}
        </div>
        <div class="mb-2 mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('language', list_of_languages(), Request::get('language', ''), ['title' => _('Language'), 'class' => 'selectpicker']) }}
        </div>
        <div class="mb-2 mr-2 mr-lg-0 ml-lg-2">
            {{ Form::select('status', list_of_status(), Request::get('status', ''), ['title' => _('Status'), 'class' => 'selectpicker']) }}
        </div>
        {{ Form::submit(_('Apply'), ['class' => 'submit btn btn-primary']) }}
    {!! Form::close() !!}
</div>