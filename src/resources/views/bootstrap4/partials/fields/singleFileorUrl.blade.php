@if ($display)

    @php
        $mediaItem = $item->getFirstMedia($field->key);
    @endphp

        <div class="form-group form-group-{{ $field->key }}">

            <label for="" title="Position: {{ $field->position }}">
                {{ $field->label }}
                @if (! empty($field->getOption('tooltip')))
                    <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                    title="{!! $field->getOption('tooltip') !!}"></i>
                 @endif
            </label>

            @if (! empty($field->getOption('help')))
                <div class="help-block" style="margin-top: -7px;">{!! $field->getOption('help') !!}</div>
            @endif

            <file-or-text :name="'{{ $field->key }}'"
                          :value="'{{ $item->{$field->key} }}'"
                          :type="'{{ empty($mediaItem) ? 'text' : 'file' }}'"
                          :class_name="'{{ $field->getOption('class') }}'"
                          :styles="'{{ $field->getOption('style') }}'"
                          :placeholder="'{{ $field->getOption('placeholder') }}'"
                          :media="{{ json_encode([$mediaItem]) }}"
                          :model_id="{{ empty($item->id) ? 0 : $item->id }}"
                          :model_type="'{{ addslashes(get_class($item)) }}'"
            ></file-or-text>

        </div>

@endif
