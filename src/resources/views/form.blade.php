@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $item->exists ? 'Edit' : 'Add' }}

                        @if($dynamo->hasFormPanelTitleOverride() == null)
                            {{ $dynamo->getName() }}
                        @else
                            {{ $dynamo->getFormPanelTitleOverride() }}
                        @endif

                        @if ($item->exists && method_exists($item, 'url'))
                            <a href="{{ $item->url() }}" target="_blank" style="margin-left: 10px;" class="btn btn-info btn-xs pull-right"><i class="fa fa-eye"></i> View</a>
                        @endif

                        @foreach ($dynamo->getFormHeaderButtons() as $button)
                                <div style="margin-left: 10px;" class="pull-right">{!! call_user_func($button) !!}</div>
                        @endforeach
                    </div>

                    <div class="panel-body">

                        @include('dynamo::partials.alerts')

                        {{--***************************************
                            *     If the user uses Formtabs       *
                            *      Run this block to render       *
                            *     the tabs                        *
                            *************************************** --}}

                        @if ($dynamo->hasFormTabs())
                             <ul class="nav nav-tabs" role="tablist">
                                @foreach ($dynamo->getFormTabs() as $index => $tab)
                                    <li role="presentation" class="{{ ($index == 0) ? 'active' : '' }}">
                                        <a href="#{{ $tab->key }}" role="tab" aria-controls="{{ $tab->key }}" data-toggle="tab">{{ $tab->getName() }}
                                            @if ($tab->hasOption('tooltip'))
                                                <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                                title="{!! $tab->getOption('tooltip') !!}"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {!! Form::model($item, $formOptions) !!}

                            <input type="hidden" id="item-id" value="{{ $item->id }}">

                            <div class="tab-content">
                                @if ($dynamo->hasFormTabs())

                                    {{--***************************************
                                        *     If the user uses Formtabs       *
                                        *     Run this block to render        *
                                        *     the form fields in each tab     *
                                        *************************************** --}}

                                    @foreach ($dynamo->getFormTabs() as $index => $tab)
                                        <div role="tabpanel" class="tab-pane{{ ($index == 0) ? ' active' : '' }}"
                                             id="{{ $tab->key }}">

                                             @foreach ($tab->fields as $field)
                                                 {!! $field->render($item) !!}
                                             @endforeach
                                        </div>
                                    @endforeach

                                @else

                                    {{--***************************************
                                        *  If the user does not use Formtabs  *
                                        *   Run this default block to render  *
                                        *   the dynamo form as normally would *
                                        *************************************** --}}

                                    @foreach ($dynamo->getFieldGroups() as $group => $fields)
                                        <fieldset id="{{ $group }}" class="{{ ! empty($group) ? 'well' : '' }} dynamo-group">
                                            @if ($dynamo->hasGroupLabel($group))
                                                <legend class="dynamo-group-label">{{ $dynamo->getGroupLabel($group) }}</legend>
                                            @endif

                                            @foreach ($fields as $field)
                                                {!! $field->render($item) !!}
                                            @endforeach
                                        </fieldset>
                                    @endforeach

                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                            @if ($dynamo->hasSaveItemTextChange() == null)
                                Save {{ $dynamo->getName() }}
                            @else
                                {{ $dynamo->getSaveItemText() }}
                            @endif
                            </button>
                            <a href="{{ route($dynamo->getRoute('index')) }}" class="btn">Cancel</a>
                        {!! Form::close() !!}

                        @if($dynamo->hasFormFooterButton())
                            <div class="d-flex justify-content-end" style="margin-top: -51px;">
                        @endif

                            @foreach ($dynamo->getFormFooterButtons() as $button)
                                    <div style="margin-left: 10px;" class="pull-right">{!! call_user_func($button) !!}</div>
                            @endforeach
                        @if($dynamo->hasFormFooterButton())
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
