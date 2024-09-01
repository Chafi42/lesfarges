<?php
extract(get_query_var('field'));
$value = get_query_var('value');
$attr = '';
if (isset($attributes) && is_array($attributes)) {
    foreach ($attributes as $key => $attrVal) {
        if ($key === 'type') {
            $type = $attrVal;
            continue;
        }
        $attr .= ' ' . $key . '="' . $attrVal . '"';
    }
}
?>

<input type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>" value="<?= $value ?>" placeholder="<?= $placeholder ?>" <?= $attr ?> />
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>