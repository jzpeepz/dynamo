<div class="form-group">
    <label for="">{{ $field->label }}</label>
    {!! Form::text($field->key, $item->{$field->key}, ['class' => 'form-control']) !!}
</div>
