@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $item->exists ? 'Edit' : 'Add' }} {{ $dynamo->getName() }}

                        @if (method_exists($item, 'url'))
                            <a href="{{ $item->url() }}" target="_blank" class="btn btn-info btn-xs pull-right">Preview</a>
                        @endif
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

                        {{--***************************************
                            *     If the user uses Formtabs       *
                            *     Run this block to render        *
                            *     the form fields in each tab     *
                            *************************************** --}}

                        @if ($dynamo->hasFormTabs())
                            {!! Form::model($item, $formOptions) !!}
                            <div class="tab-content">
                                @foreach ($dynamo->getFormTabs() as $index => $tab)
                                    <div role="tabpanel" class="tab-pane{{ ($index == 0) ? ' active' : '' }}"
                                         id="{{ $tab->key }}">

                                         @foreach ($tab->fields as $field)
                                             {!! $field->render($item) !!}
                                         @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">Save {{ $dynamo->getName() }}</button>
                            <a href="{{ route($dynamo->getRoute('index')) }}" class="btn">Cancel</a>
                            {!! Form::close() !!}
                        @endif

                        {{--***************************************
                            *  If the user does not use Formtabs  *
                            *   Run this default block to render  *
                            *   the dynamo form as normally would *
                            *************************************** --}}

                        @if (! $dynamo->hasFormTabs())
                            {!! Form::model($item, $formOptions) !!}
                            <div class="tab-content">

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

                            <button type="submit" class="btn btn-primary">Save {{ $dynamo->getName() }}</button>
                            <a href="{{ route($dynamo->getRoute('index')) }}" class="btn">Cancel</a>

                        {!! Form::close() !!}

                        @endif

                        @if (class_exists('\Uploader'))
                            {!! Uploader::helper() !!}
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
