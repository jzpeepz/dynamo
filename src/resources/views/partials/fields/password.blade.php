<div class="form-group">
    <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
    {!! Form::password($field->key, ['class' => 'form-control '.$field->getOption('class'), 'autocomplete' => 'new-password']) !!}
</div>
