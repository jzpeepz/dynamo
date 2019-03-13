@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))

    <div class="card">
        <div class="card-header">{{ $item->exists ? 'Edit' : 'Add' }} {{ $dynamo->getName() }}</div>

        <div class="card-body">

            @include('dynamo::partials.alerts')

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

            @endif {{-- endif for Form Tabs Nav --}}

            @if ($dynamo->hasFormTabs())
                <div class="tab-content">

                @foreach ($dynamo->getFormTabs() as $index => $tab)

                            <div role="tabpanel" class="tab-pane{{ ($index == 0) ? ' active' : '' }}"
                                 id="{{ $tab->key }}">
                                @foreach('of the tabs options (the names of the fields it should contain)')
                                    {{-- Render the form fields here --}}
                                @endforeach
                            </div>

                @endforeach
                </div>
            @endif {{-- endif for Form Tabs Content --}}

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
