<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>
<textarea name="<?= $name; ?>" id="<?= $name; ?>" placeholder="<?= $placeholder; ?>" rows=<?= $rows ?> cols=<?= $cols ?>><?= $value; ?></textarea>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>