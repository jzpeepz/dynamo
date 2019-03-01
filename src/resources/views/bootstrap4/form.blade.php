@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))

    <div class="card">
        <div class="card-header">{{ $item->exists ? 'Edit' : 'Add' }} {{ $dynamo->getName() }}</div>

        <div class="card-body">

            @include('dynamo::partials.alerts')

            @if ($dynamo->hasFormTabs())

                 <ul class="nav nav-tabs">
                    @foreach ($dynamo->getFormTabs() as $index => $tab)
                        {{-- First, is check to see if the tab is for the admin view or form view, only render the admin view here. --}}
                                <li class="nav-item">
                                    <a class="{{ ($index == 0 && ! request()->has('view')) || (request()->input('view') == str_slug($tab->getName())) ? 'active' : '' }}"
                                        href="{{ route($dynamo->getRoute('index'), ['view' => str_slug($tab->getName())]) }}" role="tab">{{ $tab->getName() }}</a>
                                </li>
                            {{-- @if ($thisTabHasTooltip)
                                <i style="font-size: 20px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                title="{!! $field->getOption('tooltip') !!}"></i>
                            @endif --}}
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

        </div>
    </div>

@endsection
