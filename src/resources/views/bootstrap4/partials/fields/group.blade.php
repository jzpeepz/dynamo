<?php
$group = $dynamo->getGroup($field->key);
?>

{!! $group->renderBefore() !!}

<div class="dynamo-group dynamo-group-{{ $group->name }} {{ $group->options->get('class') }}">

    <div class="dynamo-group-card card">

        <div class="card-body">

            @if ($group->options->has('label'))
                <h4 style="margin-top: 0px;">{!! $group->options->get('label') !!}
                    @if ($group->options->has('tooltip'))
                        <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle ml-2" data-toggle="tooltip" data-html="true"
                        title="{!! $group->options->get('tooltip') !!}"></i>
                    @endif
                </h4>
                
            @endif
            

            {!! $group->render($item) !!}

        </div>

    </div>

</div>

{!! $group->renderAfter() !!}
