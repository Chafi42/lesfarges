<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>
<input <?php checked($value, 1, true); ?> type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>" value="1" />
<?php if ($desc) : ?>
    <span class="description text-secondary"><?= $desc ?></span>
<?php endif; ?>