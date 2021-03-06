@if ($display)
    {!! $field->renderBefore() !!}
    <div class="dynamo-field-root {{ $field->getOption('root-class') }}">
        <div class="form-group form-group-{{ $field->key }}">
            <div class="checkbox">
                <label title="Position: {{ $field->position }}">
                    <input type="hidden" name="{{ $field->key }}" value="0">
                    <input type="checkbox" id="{{ $field->key }}" name="{{ $field->key }}" {{ $item->{$field->key} ? 'checked' : '' }} class="{{ $field->getOption('class') }}" value="1"> {{ $field->label }}
                </label>
                @if (! empty($field->getOption('help')))
                    <p class="help-block">{!! $field->getOption('help') !!}</p>
                @endif
                @if (! empty($field->getOption('tooltip')))
                    <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                        title="{!! $field->getOption('tooltip') !!}"></i>
                @endif
            </div>
        </div>
    </div>
    {!! $field->renderAfter() !!}
@endif
