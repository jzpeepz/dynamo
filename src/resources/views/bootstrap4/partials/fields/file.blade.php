@if ($display)

    <div class="form-group">
        <label for="" title="Position: {{ $field->position }}">
            {{ $field->label }}
            @if (! empty($field->getOption('help')))
                ({!! $field->getOption('help') !!})
            @endif
            @if (! empty($field->getOption('tooltip')))
                <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                    title="{!! $field->getOption('tooltip') !!}"></i>
            @endif
        </label>
        {!! Form::file($field->key, ['class' => 'form-control dynamo-file '.$field->getOption('class')]) !!}
        @if (! empty($item->{$field->key}))
            <p class="help-block">Current: <a href="{{ $item->{$field->key} }}" target="_blank">{{ $item->{$field->key} }}</a></p>
        @endif
    </div>

@endif
