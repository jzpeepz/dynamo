<div class="form-group">
    <label for="" title="Position: <?= $field->position ?>"><?= $field->label ?></label>
    {!! Form::select('<?= $field->key ?>', $dynamo->getField('<?= $field->key ?>')->getSelectOptions(), $item-><?= $field->key ?>, ['class' => 'form-control <?= $field->getOption('class') ?>']) !!}
</div>
