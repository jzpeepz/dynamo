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

                        @if ($dynamo->hasFormTabs())

                             <ul class="nav nav-tabs">
                                @foreach ($dynamo->getFormTabs() as $index => $tab)
                                    <li class="{{ ($index == 0 && ! request()->has('view')) || (request()->input('view') == str_slug($tab->getName())) ? 'active' : '' }}">
                                        <a href="{{ route($dynamo->getRoute('index'), ['view' => str_slug($tab->getName())]) }}" role="tab">{{ $tab->getName() }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- content sections for each indexTab --}}



                        @endif {{-- endif for Index Tabs --}}

                        {!! Form::model($item, $formOptions) !!}
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

                        @if (class_exists('\Uploader'))
                            {!! Uploader::helper() !!}
                        @endif

                        {{-- @if ($dynamo->hasFormTabs()) --}}

                                  {{-- <ul> --}}
                                {{-- @foreach ($dynamo->bootstrapTabs as $key => $value) --}}
                                    {{-- First, is check to see if the tab is for the admin view or form view, only render the admin view here. --}}
                                        {{-- <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#content" role="tab" data-toggle="tab">{{ $value }}</a></li>
                                        @if ($thisTabHasTooltip)
                                            <i style="font-size: 20px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                                title="{!! $field->getOption('tooltip') !!}"></i>
                                        @endif
                                @endforeach
                                    </ul> --}}


                            {{-- <ul class="nav nav-tabs" id="myTab">
                              <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
                              <li><a href="#profile" data-toggle="tab">Profile</a></li>
                              <li><a href="#messages" data-toggle="tab">Messages</a></li>
                            </ul> --}}

                        {{-- @endif --}} {{-- endif for Form Tabs --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
