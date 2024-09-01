<?php
extract(get_query_var('field'));
$value = get_query_var('settings');
$attr = '';
if (isset($attributes) && is_array($attributes)) {
    foreach ($attributes as $attr_name => $attr_value) {
        if ($attr_name == 'type') {
            $type = $attr_value;
            continue;
        }
        $attr .= $attr_name . '="' . $attr_value . '" ';
    }
}
?>

<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" <?= $attr ?> />
<?php if ($desc != '') {
    echo '<p class="description">' . $desc . '</p>';
} ?>