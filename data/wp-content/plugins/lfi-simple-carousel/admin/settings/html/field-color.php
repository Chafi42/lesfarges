<?php
extract(get_query_var('field'));
$colClass = 'col-auto py-1 pl-0 pr-2';
$inputClass = $input ? 'color-input' : 'color-input d-none';
$colors = get_query_var('value');
// $swatches = isset($field['swatches']) ? $field['swatches'] : '';

if (!is_array($colors)) {
    $colors = array($colors);
}

$pickerData = htmlspecialchars(
    json_encode(array(
        'inputName' => $name . '[' . $default . ']',
        'inputValue' => '',
        'defaultColor' => $default,
        'multi' => $multi,
        'swatches' => $swatches,
        'groupClass' => $colClass,
        'inputClass' => $inputClass,
    )),
    ENT_QUOTES,
    'UTF-8'
);

?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>
<div id="<?= $name ?>" class="container-fluid p-0 color-field" data-picker="<?= $pickerData ?>">
    <div class="row no-gutters">
        <?php if (is_array($colors)) : ?>
            <?php foreach ($colors as $colorHex => $colorName) : ?>
                <?php $inputName = $name . '[' . $colorHex . ']'; ?>
                <div class="<?= $colClass ?> group-color">
                    <div class="color-pickr d-inline-block"></div>
                    <input type="text" name="<?= $inputName ?>" value="<?= $colorName ?>" class="<?= $inputClass ?>" />
                    <span class="del-color button bg-transparent border-0 text-danger pl-1">
                        <i class="fas fa-times"></i>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($multi || empty($colors)) : ?>
            <div class="<?= $colClass ?> add-color">
                <span class="button rounded-circle text-dark border-dark bg-transparent">
                    <i class="fas fa-plus"></i>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>