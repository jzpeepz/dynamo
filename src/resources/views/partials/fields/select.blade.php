<div class="form-group">
    <label for="">{{ $field->label }}</label>
    {!! Form::select($field->key, $field->options['options'], $item->{$field->key}, ['class' => 'form-control']) !!}
</div>
