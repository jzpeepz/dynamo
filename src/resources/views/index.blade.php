@extends('layouts.app')

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

                        @if ($items->isEmpty())

                            <div>No items found. <a href="{{ route($dynamo->getRoute('create')) }}">Add one.</a></div>

                        @else

                            <table class="table">
                                <thead>
                                    <tr>
                                        @foreach ($dynamo->getIndexes() as $index)
                                            <th>{{ $index->label }}</th>
                                        @endforeach
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            @foreach ($dynamo->getIndexes() as $index)
                                                <td>{{ $item->{$index->key} }}</td>
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

                            {{ $items->links() }}

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
