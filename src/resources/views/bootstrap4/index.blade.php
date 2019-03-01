@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section(config('dynamo.target_blade_section', 'content'))

    <div class="card">
        <div class="card-header">
            @if ($dynamo->addVisible())
                <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-sm float-right">Add {{ $dynamo->getName() }}</a>
            @endif
            {{ $dynamo->getName() }} Manager
        </div>

        <div class="card-body">

            @include('dynamo::partials.alerts')

            <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search form-inline">

                @foreach ($dynamo->getFilters() as $filter)

                    {!! $filter !!}

                @endforeach

                @if ($dynamo->hasSearchable())

                    <div class="form-group">
                        <label for="" class="search-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" value="{{ request()->input('q') }}" {!! $dynamo->getSearchOptionsString() !!}>
                            <span class="input-group-btn">
                                @if (request()->has('q'))
                                    <a href="{{ route($dynamo->getRoute('index')) }}" class="btn btn-light">Clear</a>
                                @endif
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                            </span>
                        </div>
                    </div>

                @endif

                {{-- BOOTSTRAP TAB IMPLEMENTATION  --}}
                {{-- @if (! empty($field->getOption('tooltip')))
                    <i style="font-size: 20px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                        title="{!! $field->getOption('tooltip') !!}"></i>
                @endif --}}
                @if ($dynamo->hasIndexTabs())

                     <ul class="nav nav-tabs" role="tablist">
                        @foreach ($dynamo->getIndexTabs() as $index => $tab)
                                    <li class="nav-item">
                                        <a class="nav-link {{ ($index == 0 && ! request()->has('view')) || (request()->input('view') == str_slug($tab->getName())) ? 'active' : '' }}"
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

                {{-- <ul class="nav nav-tabs" role="tablist"> --}}
                    {{-- <li class="nav-item">
                        <a class="nav-link active" href="#">Active</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link disabled" href="#">Disabled</a>
                      </li> --}}

            </form>

            @if ($items->isEmpty())

                <div>No items found. <a href="{{ route($dynamo->getRoute('create')) }}">Add one.</a></div>

            @else

                <table class="table" id="dynamo-index">
                    <thead>
                        <tr>
                            @foreach ($dynamo->getIndexes() as $index)
                                <th>{{ $index->label }}</th>
                            @endforeach
                            <th style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="dynamo-index-body">
                        @foreach ($items as $item)
                            <tr class="dynamo-index-row" data-id="{{ $item->id }}">
                                @foreach ($dynamo->getIndexes() as $index)
                                    <td>{!! $index->getValue($item) !!}</td>
                                @endforeach
                                <td style="width: 150px;">
                                    <a href="{{ route($dynamo->getRoute('edit'), $item->id) }}" class="btn btn-light btn-sm">Edit</a>

                                    @if ($dynamo->deleteVisible())
                                        {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                                            <button class="btn btn-light btn-sm btn-delete">Delete</button>
                                        {!! Form::close() !!}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! method_exists($items, 'render') ? $items->appends(request()->only(['q']))->render() : null !!}

            @endif
        </div>
    </div>

    <style>
    .panel-body .table { margin-bottom: 0; }
    .dynamo-search label { display: block; }
    .dynamo-search label.search-label { visibility: hidden; }
    </style>
@endsection

@section('scripts')
    <script>
    $(document).ready(function(){
        $('.btn-delete').click(function(){
            return confirm('Are you sure?');
        });
    });
    </script>
@endsection
