<div class="form-group">
    <label for="" title="Position: <?= $field->position ?>"><?= $field->label ?></label>
    {!! Form::text('<?= $field->key ?>', $item-><?= $field->key ?>, ['class' => 'form-control <?= $field->getOption('class') ?>']) !!}
</div>