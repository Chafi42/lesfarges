<?php
extract(get_query_var('field'));
$colClass = 'col-auto py-1 pl-0 pr-2';
$inputClass = $input ? 'color-input' : 'color-input d-none';
$colors = get_query_var('value');
$swatches = $swatches ? $swatches : '';

if (!is_array($colors)) {
    $colors = array($colors => '');
}

// DEBUG
// echo "<pre>";
// echo print_r($colors, true);
// echo "</pre>";

$defaultColor = is_array($default) ? array_key_first($default) : $default;


$pckrInputName = $input ? $name . '[' . reset($default) . ']' : $name;
$pickerData = htmlspecialchars(
    json_encode(array(
        'inputName' => $pckrInputName,
        'inputValue' => '',
        'defaultColor' => $defaultColor,
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
<div id="<?= $name ?>" class="container-fluid p-0 color-field" data-picker="<?= $pickerData ?>" data-input="<?= $input ?>" data-multi="<?= $multi ?>">
    <div class="row no-gutters">
        <?php if (is_array($colors)) : ?>
            <?php foreach ($colors as $colorHex => $colorName) : ?>
                <?php
                $inputValue = $input ? $colorName : $colorHex;
                $inputName =  $input ? $name . '[' . $colorHex . ']' : $name;
                ?>
                <div class="<?= $colClass ?> group-color">
                    <div class="color-pickr d-inline-block"></div>
                    <input type="text" name="<?= $inputName ?>" value="<?= $inputValue ?>" class="<?= $inputClass ?>" />
                    <span class="del-color button bg-transparent border-0 text-danger pl-1">
                        <i class="fas fa-times"></i>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php
        // If Multiple inputs - add 
        if ($multi || empty($colors)) : ?>
            <div class="<?= $colClass ?> add-color">
                <span class="button rounded-circle text-dark border-dark bg-transparent">
                    <i class="fas fa-plus"></i>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>