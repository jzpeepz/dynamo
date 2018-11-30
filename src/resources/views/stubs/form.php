@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $item->exists ? 'Edit' : 'Add' }} <?= $dynamo->getName() ?></div>

                    <div class="panel-body">

                        @include('dynamo::partials.alerts')

                        {!! Form::model($item, $formOptions) !!}
                            <?php foreach ($dynamo->getFieldGroups() as $group => $fields): ?><fieldset id="<?= $group ?>" class="<?= ! empty($group) ? 'well' : '' ?> dynamo-group">

                                @if ($dynamo->hasGroupLabel('<?= $group ?>'))
                                    <legend class="dynamo-group-label">{{ $dynamo->getGroupLabel('<?= $group ?>') }}</legend>
                                @endif

    <?php foreach ($fields as $field): ?><?= $field->renderStub() . "\n" ?><?php endforeach; ?>
                            </fieldset><?php endforeach; ?>


                            <button type="submit" class="btn btn-primary">Save {{ $dynamo->getName() }}</button>
                            <a href="{{ route($dynamo->getRoute('index')) }}" class="btn">Cancel</a>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
