<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::textarea($field->key, isset($item) ? $item->{$field->key} : null, ['class' => 'form-control '.$field->getOption('class')]) !!}
    @if (! empty($field->getOption('help')))
        <p class="help-block">{!! $field->getOption('help') !!}</p>
    @endif
</div>
