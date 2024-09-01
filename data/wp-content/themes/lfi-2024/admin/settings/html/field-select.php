<?php
extract(get_query_var('field'));
$value = get_query_var('value');
?>
<select class="select-2" name="<?php echo $name; ?>" id="<?php echo $name; ?>">
    <option value=""><?= $default ?></option>
    <?php
    foreach ($options as $key => $text) {
        echo '<option ' . selected($key, $value, false) . ' value="' . $key . '">' . $text . '</option>';
    }
    ?>
</select>
<?php if ($desc != '') {
    echo '<p class="description">' . $desc . '</p>';
} ?>