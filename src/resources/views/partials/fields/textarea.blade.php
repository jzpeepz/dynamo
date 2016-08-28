<div class="form-group">
    <label for="">{{ $field->label }}</label>
    {!! Form::textarea($field->key, $item->{$field->key}, ['class' => 'form-control']) !!}
</div>
