{!! $field->renderBefore() !!}
<div class="dynamo-field-root {{ $field->getOption('root-class') }}">
    <div class="{{ $display ? 'd-block' : 'd-none' }}">

        <div class="form-group form-group-{{ $field->key }}">
            <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
            @if (! empty($field->getOption('help')))
                <p class="help-block">{!! $field->getOption('help') !!}</p>
            @endif
            @if (! empty($field->getOption('tooltip')))
                <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                    title="{!! $field->getOption('tooltip') !!}"></i>
            @endif
            {!! Form::select(
                $field->key.'[]',
                $field->options['options'],
                isset($field->options['value']) ? call_user_func($field->options['value'], $item, $field) : $item->{$field->key}->pluck('id')->toArray(),
                [
                    'multiple' => true,
                    'class' => 'form-control '.$field->getOption('class')
                ]
            ) !!}
        </div>

    </div>
</div>
{!! $field->renderAfter() !!}
