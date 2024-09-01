<?php
extract(get_query_var('field'));
$colClass = 'col-auto py-1 pl-0 pr-2 ';
$default = is_array($default) ? $default : array($default => '');
?>
<div id="<?= $name ?>" class="container-fluid p-0 color-field">
    <div class="row no-gutters">
        <div class="<?= $colClass ?>color-group base-input d-none">
            <div class="color-pickr d-inline-block"></div>
            <input type="text" name="<?= $name; ?>[][]" value="#bada55" class="d-none color-hex" />
            <input type="text" name="<?= $name ?>[][]" value="" class="color-name">
            <span class="del-color button bg-transparent border-0 text-danger pl-1">
                <i class="fas fa-times"></i>
            </span>
        </div>
        <?php $cpt = 0; ?>

        <?php foreach ($default as $colorHex => $colorName) : ?>
            <?php
            $inputName = $multi ? $name . '[' . $cpt . '][]' : $name . '[]';
            ?>
            <div class="<?= $colClass ?>color-group options">
                <div class="color-pickr d-inline-block"></div>
                <input type="text" name="<?= $inputName ?>" value="<?= $colorHex ?>" class="d-none color-hex" />
                <?php if ($input) : ?>
                    <input type="text" name="<?= $inputName ?>" value="<?= $colorName ?>" class="color-name" />
                <?php endif; ?>
                <?php if ($multi) : ?>
                    <span class="del-color button bg-transparent border-0 text-danger pl-1">
                        <i class="fas fa-times"></i>
                    </span>
                <?php endif; ?>
            </div>
            <?php $cpt++; ?>
        <?php endforeach; ?>

        <?php if ($multi) : ?>
            <div class="<?= $colClass ?>add-color">
                <span class="button rounded-circle text-dark border-dark bg-transparent">
                    <i class="fas fa-plus"></i>
                </span>
                <input type="hidden" name="<?= $name ?>[multi]" value="1">
            </div>
        <?php endif; ?>
    </div>
</div>