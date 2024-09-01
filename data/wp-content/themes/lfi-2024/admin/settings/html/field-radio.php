<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>
<?php foreach ($options as $key => $text) : ?>
    <input type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>" value="<?= $key ?>" <?= checked($value, $key) ?>> <?= $text ?><br>
<?php endforeach; ?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>