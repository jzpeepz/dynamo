@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10">
                <div class="card">
                    <div class="card-header">
                        {{ $item->exists ? 'Edit' : 'Add' }} {{ $dynamo->getName() }}

                        @if (method_exists($item, 'url'))
                            <a href="{{ $item->url() }}" target="_blank" class="btn btn-info btn-sm pull-right">Preview</a>
                        @endif
                    </div>

                    <div class="card-body">

                        @include('dynamo::partials.alerts')

                        {{--***************************************
                            *     If the user uses Formtabs       *
                            *      Run this block to render       *
                            *     the tabs                        *
                            *************************************** --}}

                        @if ($dynamo->hasFormTabs())
                             <ul class="nav nav-tabs" role="tablist">
                                @foreach ($dynamo->getFormTabs() as $index => $tab)
                                    <li role="presentation" class="nav-item{{ ($index == 0) ? ' active' : '' }}">
                                        <a class="nav-link" href="#{{ $tab->key }}" role="tab" aria-controls="{{ $tab->key }}" data-toggle="tab">{{ $tab->getName() }}
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

                            <button type="submit" class="btn btn-primary">Save {{ $dynamo->getName() }}</button>
                            <a href="{{ route($dynamo->getRoute('index')) }}" class="btn">Cancel</a>


                            @if (method_exists($item, 'url'))
                                @if ($dynamo->deleteVisible())
                                    {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'class' => 'delete-form pull-right', 'style' => 'display: inline-block;', 'onsubmit' => 'return confirm(\'Are you sure?\');']) !!}
                                        <button class="btn btn-danger btn-delete" style="float: right;">Delete</button>
                                    {!! Form::close() !!}

                                @endif
                            @endif
                        {!! Form::close() !!}



                        @if (class_exists('\Uploader'))
                            {!! Uploader::helper() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
