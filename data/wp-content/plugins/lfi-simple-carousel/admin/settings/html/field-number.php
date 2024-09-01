<?php
extract(get_query_var('field'));
$setting = get_query_var('value');
?>

<input type="number" name="<?= $name ?>" value="<?= $setting ?>" min="<?= $min ?>" max="<?= $max ?>" step="<?= $step ?>">
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>