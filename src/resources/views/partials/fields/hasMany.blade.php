<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::select($field->key.'[]', $field->options['options'], $item->{$field->key}->lists('id')->toArray(), ['multiple' => true, 'class' => 'form-control '.$field->getOption('class')]) !!}
</div>
