@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-xs pull-right">Add {{ $dynamo->getName() }}</a>
                        {{ $dynamo->getName() }} Manager
                    </div>

                    <div class="panel-body">

                        @include('dynamo::partials.alerts')

                        @if ($dynamo->hasSearchable())
                            <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search">

                                <div class="form-group">
                                    <label for="" class="sr-only">Search</label>
                                    <div class="input-group">
                                        <input type="text" name="q" class="form-control" placeholder="" value="{{ request()->input('q') }}">
                                        <span class="input-group-btn">
                                            @if (request()->has('q'))
                                                <a href="{{ route($dynamo->getRoute('index')) }}" class="btn btn-default">Clear</a>
                                            @endif
                                            <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>

                            </form>
                        @endif

                        @if ($items->isEmpty())

                            <div>No items found. <a href="{{ route($dynamo->getRoute('create')) }}">Add one.</a></div>

                        @else

                            <table class="table">
                                <thead>
                                    <tr>
                                        @foreach ($dynamo->getIndexes() as $index)
                                            <th>{{ $index->label }}</th>
                                        @endforeach
                                        <th style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            @foreach ($dynamo->getIndexes() as $index)
                                                <td>{{ $dynamo->getValue($index->key, $item) }}</td>
                                            @endforeach
                                            <td>
                                                <a href="{{ route($dynamo->getRoute('edit'), $item->id) }}" class="btn btn-default btn-xs">Edit</a>
                                                {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                                                    <button class="btn btn-default btn-xs btn-delete">Delete</button>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ method_exists($items, 'links') ? $items->links() : null }}

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
