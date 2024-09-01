<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>

<div class="d-flex">
    <input type="range" class="pointer" name="<?= $name ?>" value="<?= $value ?>" min="<?= $min ?>" max="<?= $max ?>" step="<?= $step ?>">
    <span class="output font-weight-bold ml-3"><?= $value ?></span>
</div>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>