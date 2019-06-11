<?php
$group = $dynamo->getGroup($field->key);
?>

{!! $group->renderBefore() !!}

<div class="dynamo-group dynamo-group-{{ $group->name }} {{ $group->options->get('class') }}">

    <div class="dynamo-group-card card">

        <div class="card-body">

            @if ($group->options->has('label'))
                <h4 style="margin-top: 0px;">{!! $group->options->get('label') !!}</h4>
            @endif

            {!! $group->render($item) !!}

        </div>

    </div>

</div>

{!! $group->renderAfter() !!}
