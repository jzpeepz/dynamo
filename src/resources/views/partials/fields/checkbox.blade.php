@if ($display)

    <div class="form-group">
        <div class="checkbox">
            <label title="Position: {{ $field->position }}">
                <input type="hidden" name="{{ $field->key }}" value="0">
                <input type="checkbox" id="{{ $field->key }}" name="{{ $field->key }}" {{ $item->{$field->key} ? 'checked' : '' }} class="{{ $field->getOption('class') }}" value="1"> {{ $field->label }}
            </label>
            @if (! empty($field->getOption('tooltip')))
                <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                    title="{!! $field->getOption('tooltip') !!}"></i>
            @endif
        </div>
    </div>

@endif
