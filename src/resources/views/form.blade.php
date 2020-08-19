@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $item->exists ? 'Edit' : 'Add' }}

                        @if($dynamo->hasFormPanelTitleOverride() == null)
                            {{ $dynamo->getName() }}
                        @else
                            {!! $dynamo->getFormPanelTitleOverride() !!}
                        @endif

                        @if ($item->exists && method_exists($item, 'url'))
                            <a href="{{ $item->url() }}" target="_blank" style="margin-left: 10px;" class="btn btn-info btn-xs pull-right"><i class="fa fa-eye"></i> View</a>
                        @endif

                    </div>

                    <div class="panel-body">

                        @include('dynamo::partials.alerts')
                        
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

                            <button type="submit" class="btn btn-primary">
                                @if ($dynamo->hasSaveItemTextChange() == null)
                                    Save {{ $dynamo->getName() }}
                                @else
                                    {{ $dynamo->getSaveItemText() }}
                                @endif
                                </button>
                            <a href="{{ route($dynamo->getRoute('index'), $dynamo->getRouteParameters('index')) }}" class="btn">Cancel</a>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
