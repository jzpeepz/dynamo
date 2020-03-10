@if ($display)

    @php
        $mediaItems = $item->getMedia($field->key);
    @endphp

    <div class="form-group form-group-gallery">

        <label for="" title="Position: {{ $field->position }}">
            {{ $field->label }}
            @if (! empty($field->getOption('help')))
                ({!! $field->getOption('help') !!})
            @endif
            @if (! empty($field->getOption('tooltip')))
                <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="{!! $field->getOption('tooltip') !!}"></i>
            @endif
        </label>

        <gallery-manager
            :name="'{{ $field->key }}'"
            :media="{{ $mediaItems->toJson() }}"
            :model_id="{{ empty($item->id) ? 0 : $item->id }}"
            :model_type="'{{ addslashes(get_class($item)) }}'"
        ></gallery-manager>

    </div>

@endif
