@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">

                    {{--*****************************************************************
                        *                         START HEADER BLOCK                    *
                        *            This block is the header of the dynamo panel        *
                        *    Includes name of manager, index buttons, and create button *
                        ***************************************************************** --}}

                    <div class="panel-heading">
                        @if ($dynamo->addVisible())
                            <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-xs pull-right">
                                @if ($dynamo->hasAddItemTextChange() == null)
                                Add {{ $dynamo->getName() }}
                                @else
                                    {{ $dynamo->getAddItemText()}}
                                @endif
                            </a>
                        @endif

                        @foreach ($dynamo->getIndexButtons() as $button)
                            <div class="pull-right" style="margin-right: 5px;">{!! call_user_func($button) !!}</div>
                        @endforeach

                        @if($dynamo->hasIndexPanelTitleOverride() == null)
                            {{ $dynamo->getName() }} Manager
                        @else
                            {!! $dynamo->getIndexPanelTitleOverride() !!}
                        @endif

                    </div>

                    {{--***************************
                        *     END HEADER BLOCK    *
                        *************************** --}}

                    {{--**************************************
                            *       START SEARCH BAR BLOCK       *
                            *  This block begins the panel-body   *
                            *     Includes search bar form       *
                            ************************************** --}}

                    <div class="panel-body">

                        <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search">

                        <input type="hidden" name="view" class="form-control" value="{{ request()->input('view') }}">

                        @include('dynamo::partials.alerts')

                        @if ($dynamo->hasSearchable())
                        
                                <div class="form-group">
                                    <label for="" class="search-label">Search</label>
                                    <div class="input-group" style="margin-top: -20px;">
                                        <input type="text" name="q" class="form-control" value="{{ request()->input('q') }}" {!! $dynamo->getSearchOptionsString() !!}>
                                        <span class="input-group-btn">
                                            @if (request()->has('q'))
                                                <a href="{{ route($dynamo->getRoute('index'), request()->only('view')) }}" class="btn btn-default">Clear</a>
                                            @endif
                                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                                        </span>
                                    </div>
                                </div>

                            @endif

                            {{--*******************************
                                *     END SEARCH BAR BLOCK    *
                                ******************************* --}}

                                 {{--*********************************************************
                                    *                 START INDEXTAB BLOCK                  *
                                    *             This block renders indexTabs              *
                                    *   Includes indexTabs, count of members in that tab,   *
                                    *                and tooltps for each tab               *
                                    ********************************************************* --}}

                            @if ($dynamo->hasIndexTabs())
                                 <ul class="nav nav-tabs" style="margin-top: 15px;">
                                    @foreach ($dynamo->getIndexTabs() as $index => $tab)
                                        <li class="{{ ($index == 0 && ! request()->has('view')) || (request()->input('view') == $tab->getViewName()) ? 'active' : '' }}">
                                            <a href="{{ route($dynamo->getRoute('index'), ['view' => $tab->getViewName()]) }}" role="tab">
                                                {{ $tab->getName() }}
                                                @if ($tab->shouldShowCount())
                                                    <span class="round-badge">{{ $dynamo->getIndexItemsQueryBuilder($tab->getViewName())->count() }}</span>
                                                @endif
                                                @if ($tab->hasOption('tooltip'))
                                                    <i style="font-size: 17px; padding-left: 2px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                                    title="{!! $tab->getOption('tooltip') !!}"></i>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            @endif

                        </form>
                        {{--*****************************
                            *     END INDEXTAB BLOCK    *
                            ***************************** --}}

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
                                            <td>
                                                <a href="{{ route($dynamo->getRoute('edit'), $item->id) }}" class="btn btn-default btn-xs">Edit</a>

                                                @foreach ($dynamo->getActionButtons() as $button)
                                                    {!! call_user_func($button, $item) !!}
                                                @endforeach

                                                @if ($dynamo->deleteVisible())
                                                    {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                                                        <button class="btn btn-default btn-xs btn-delete">Delete</button>
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
            </div>
        </div>
    </div>

    <style>
    .panel-body .table { margin-bottom: 0; }
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
