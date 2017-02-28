<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::select($field->key.'[]', $field->options['options'], $item->{$field->key}->lists('id')->toArray(), ['multiple' => true, 'class' => 'form-control '.$field->getOption('class')]) !!}
    @if (! empty($field->getOption('help')))
        <p class="help-block">{!! $field->getOption('help') !!}</p>
    @endif
</div>
