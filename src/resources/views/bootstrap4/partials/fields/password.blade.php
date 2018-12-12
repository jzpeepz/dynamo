@if ($display)

    <div class="form-group">
        <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
        {!! Form::password($field->key, ['class' => 'form-control '.$field->getOption('class'), 'autocomplete' => 'new-password']) !!}
        @if (! empty($field->getOption('help')))
            <p class="help-block">{!! $field->getOption('help') !!}</p>
        @endif
    </div>

@endif
