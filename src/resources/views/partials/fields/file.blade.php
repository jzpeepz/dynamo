<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::file($field->key, ['class' => 'form-control '.$field->getOption('class')]) !!}
    @if (! empty($item->{$field->key}))
        <p class="help-block">Current: <a href="{{ $item->{$field->key} }}" target="_blank">{{ $item->{$field->key} }}</a></p>
    @endif
</div>
