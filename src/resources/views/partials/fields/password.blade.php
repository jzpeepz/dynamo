<div class="form-group">
    <label for="">{{ $field->label }}</label>
    {!! Form::password($field->key, ['class' => 'form-control', 'autocomplete' => 'new-password']) !!}
</div>
