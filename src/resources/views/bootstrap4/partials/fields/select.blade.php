@if ($display)

    <div class="form-group">
        <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
        {!! Form::select($field->key, $field->getSelectOptions(), $item->{$field->key}, ['class' => 'form-control '.$field->getOption('class')]) !!}
        @if (! empty($field->getOption('help')))
            <p class="help-block">{!! $field->getOption('help') !!}</p>
        @endif
    </div>

@endif
