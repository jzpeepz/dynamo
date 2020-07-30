@if ($display)

    @php
        $mediaItems = $item->getMedia($field->key);
    @endphp

    {!! $field->renderBefore() !!}
    <div class="dynamo-field-root {{ $field->getOption('root-class') }}">

        <div class="form-group form-group-gallery form-group-{{ $field->key }}">

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

            <gallery-manager
                :name="'{{ $field->key }}'"
                :media="{{ $mediaItems->toJson() }}"
                :model_id="{{ empty($item->id) ? 0 : $item->id }}"
                :model_type="'{{ addslashes(get_class($item)) }}'"
            ></gallery-manager>

        </div>

    </div>
    {!! $field->renderAfter() !!}

@endif
