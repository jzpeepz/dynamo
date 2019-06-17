<?php
$group = $dynamo->getGroup($field->key);
?>

@if (! $group->isEmpty() && $group->shouldDisplay($item))

    {!! $group->renderBefore() !!}

    <div class="dynamo-group dynamo-group-{{ $group->name }} {{ $group->options->get('class') }}">

        <div class="dynamo-group-card well">

            @if ($group->options->has('label'))
                <h4 style="margin-top: 0px;">{!! $group->options->get('label') !!}</h4>
            @endif

            {!! $group->render($item) !!}

        </div>

    </div>

    {!! $group->renderAfter() !!}

@endif
