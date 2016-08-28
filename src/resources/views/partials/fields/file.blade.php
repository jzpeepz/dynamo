<div class="form-group">
    <label for="">{{ $field->label }}</label>
    {!! Form::file($field->key, ['class' => 'form-control']) !!}
    @if (! empty($item->{$field->key}))
        <p class="help-block">Current: {{ $item->{$field->key} }}</p>
    @endif
</div>
