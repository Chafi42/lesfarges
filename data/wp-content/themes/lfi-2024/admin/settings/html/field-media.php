<?php
extract(get_query_var('field'));
$value = get_query_var('value');

// Get WordPress' media upload URL
// $upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
$upload_link = esc_url(get_upload_iframe_src());

// See if there's a media id already saved as post meta
// $your_img_id = get_post_meta($post->ID, '_your_img_id', true);
$your_img_id = $value;

// Get the image src
$your_img_src = wp_get_attachment_image_src($value, 'thumbnail');

// For convenience, see if the array is valid
$you_have_img = is_array($your_img_src);
?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>
<!-- Your image container, which can be manipulated with js -->
<div class="custom-img-container">
    <?php if ($you_have_img) : ?>
        <img src="<?php echo $your_img_src[0] ?>" alt="" style="max-width:100%;" />
    <?php endif; ?>
</div>

<!-- Your add & remove image links -->
<p class="hide-if-no-js">
    <a class="upload-custom-img <?php if ($you_have_img) {
                                    echo 'hidden';
                                } ?>" href="<?php echo $upload_link ?>">
        <?= $add_image ?>
    </a>
    <a class="delete-custom-img <?php if (!$you_have_img) {
                                    echo 'hidden';
                                } ?>" href="#">
        <?= $remove_image ?>
    </a>
</p>

<!-- A hidden input to set and post the chosen image id -->
<input class="custom-img-id" name="<?= $name ?>" type="hidden" value="<?php echo esc_attr($your_img_id); ?>" />