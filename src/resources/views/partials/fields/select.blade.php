<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::select($field->key, $field->options['options'], $item->{$field->key}, ['class' => 'form-control']) !!}
</div>
