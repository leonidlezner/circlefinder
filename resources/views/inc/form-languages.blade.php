@if(\App\Language::count())
<h2>Languages</h2>
<ul class="form-check">
    @foreach(\App\Language::all() as $language)
    <li>
        {{ Form::checkbox('languages[]', $language->code, isset($item) && $item->languages->contains($language), ['class' => 'form-check-input', 'id' => 'language-' . $language->code]) }} 
        {{ Form::label('language-' . $language->code, $language->title) }}
    </li>
    @endforeach
</ul>
@endif