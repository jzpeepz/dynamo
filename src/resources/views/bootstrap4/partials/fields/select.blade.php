@if ($display)

    <?php
    $multiple = ! empty($field->getOption('multiple'));
    ?>

    <div class="form-group form-group-{{ $field->key }}">
        <label for="" title="Position: {{ $field->position }}">{{ $field->label }}</label>
        @if (! empty($field->getOption('help')))
            <p class="help-block">{!! $field->getOption('help') !!}</p>
        @endif
        @if (! empty($field->getOption('tooltip')))
            <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="{!! $field->getOption('tooltip') !!}"></i>
        @endif
        {!! Form::select($field->key . ($multiple ? '[]' : ''), $field->getSelectOptions(), $item->{$field->key}, $field->getHtmlAttributes(['class' => 'form-control '.$field->getOption('class'), 'multiple' => $multiple]) ) !!}
    </div>

@endif
