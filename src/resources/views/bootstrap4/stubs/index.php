@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section(config('dynamo.target_blade_section', 'content'))

    <div class="card">
        <div class="card-header">
            @if ($dynamo->addVisible())
                <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-sm float-right">Add {{ $dynamo->getName() }}</a>
            @endif
            <?= $dynamo->getName() ?> Manager
        </div>

        <div class="card-body">

            @include('dynamo::partials.alerts')

            @if ($dynamo->hasSearchable())
                <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search">

                    <div class="form-group">
                        <label for="" class="sr-only">Search</label>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="" value="{{ request()->input('q') }}">
                            <span class="input-group-btn">
                                @if (request()->has('q'))
                                    <a href="{{ route($dynamo->getRoute('index')) }}" class="btn btn-light">Clear</a>
                                @endif
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                            </span>
                        </div>
                    </div>

                </form>
            @endif

            @if ($items->isEmpty())

                <div>No items found. <a href="{{ route($dynamo->getRoute('create')) }}">Add one.</a></div>

            @else

                <table class="table" id="dynamo-index">
                    <thead>
                        <tr>
                            <?php foreach ($dynamo->getIndexes() as $index): ?><th><?= $index->label ?></th><?php endforeach; ?>

                            <th style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="dynamo-index-body">
                        @foreach ($items as $item)
                            <tr class="dynamo-index-row" data-id="{{ $item->id }}">
                                <?php foreach ($dynamo->getIndexes() as $index): ?><td>{!! $dynamo->getIndexValue('<?= $index->key ?>', $item) !!}</td><?php endforeach; ?>

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
