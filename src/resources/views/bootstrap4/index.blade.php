@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section(config('dynamo.target_blade_section', 'content'))

    <div class="card">
        <div class="card-header">
            @if ($dynamo->addVisible())
                <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-sm float-right">Add {{ $dynamo->getName() }}</a>
            @endif

            @foreach ($dynamo->getIndexButtons() as $button)
                <div class="mr-2 float-right">{!! call_user_func($button) !!}</div>
            @endforeach

            {{ $dynamo->getName() }} Manager
        </div>

        <div class="card-body">

            @include('dynamo::partials.alerts')

            <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search form-inline">

                @foreach ($dynamo->getFilters() as $filter)

                    {!! $filter !!}

                @endforeach

                @if ($dynamo->hasSearchable())

                    <div class="form-group mb-3">

                        <label for="" class="search-label">Keywords</label>

                        <div class="input-group">

                            <input type="text" name="q" class="form-control" placeholder="" value="{{ request()->input('q') }}" style="border-radius: .25rem;">

                            <button class="btn btn-primary ml-2" type="submit"><i class="fa fa-search"></i> Search</button>

                            @if (request()->has('q'))
                                <a href="{{ route($dynamo->getRoute('index')) }}" class="btn btn-light ml-2">Clear</a>
                            @endif

                        </div>

                    </div>

                @endif

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

                                    @foreach ($dynamo->getActionButtons() as $button)
                                        {!! call_user_func($button, $item) !!}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! method_exists($items, 'render') ? $items->appends(request()->only(['q']))->render() : null !!}

            @endif
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    $(document).ready(function(){
        $('.btn-delete').click(function(){
            return confirm('Are you sure?');
        });
    });
    </script>

    <style>
    .dynamo-search .form-group {
        flex-flow: column wrap;
        align-items: start;
    }
    .dynamo-search .form-group label {
        align-items: start;
    }
    .dynamo-search .form-group .search-label {
        /* visibility: hidden; */
    }
    </style>
@endsection
