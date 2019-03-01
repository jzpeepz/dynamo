<?php
$group = $dynamo->getGroup($field->key);
?>

{!! $group->renderBefore() !!}

<div class="dynamo-group dynamo-group-{{ $group->name }} {{ $group->options->get('class') }}">

    <div class="dynamo-group-card card">

        <div class="card-body">

            @if ($group->options->has('label'))
                <div class="card-header">{!! $group->options->get('label') !!}</div>
            @endif

            {!! $group->render($item) !!}

        </div>

    </div>

</div>

{!! $group->renderAfter() !!}
