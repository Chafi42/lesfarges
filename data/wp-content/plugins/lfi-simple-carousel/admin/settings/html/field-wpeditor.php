<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>
<?php wp_editor($value, $name, array('wpautop' => false)); ?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>