<div class="form-group">
    <div class="checkbox">
        <label title="Position: <?= $field->position ?>">
            <input type="hidden" name="<?= $field->key ?>" value="0">
            <input type="checkbox" id="<?= $field->key ?>" name="<?= $field->key ?>" {{ $item-><?= $field->key ?> ? 'checked' : '' }} class="<?= $field->getOption('class') ?>" value="1"> <?= $field->label ?>
        </label>
    </div>
</div>
