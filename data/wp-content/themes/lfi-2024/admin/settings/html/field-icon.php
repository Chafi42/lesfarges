<?php
extract(get_query_var('field'));
$value = get_query_var('value')
?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>
<div class="btn-group border rounded border-dark">
    <button type="button" class="icp icp-dd btn btn-light dropdown-toggle" data-selected="fa-car" data-toggle="dropdown" aria-expanded="true">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu iconpicker-container"></div>
    <span class="iconpicker-component p-2 px-3">
        <i class="<?= $value ?> iconpicker-component"></i>
        <input type="text" hidden value="<?= $value ?>" name="<?= $name ?>">
    </span>
</div>